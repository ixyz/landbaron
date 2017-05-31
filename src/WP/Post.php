<?php namespace Ixyz\Landbaron\WP;

use \WP_Post;

class Post
{
    /**
     * @param WP_Post $wp
     * @return Post
     */
    public static function instance(WP_Post $wp)
    {
        return new static($wp);
    }

    /**
     * @param string $id
     * @param string ...$args
     * @return Post
     */
    public static function instanceById($id, ...$args)
    {
        return new static(call_user_func_array('get_post', array_merge([ $id ], $args)));
    }

    private $wp = null;

    /**
     * @param WP_Post $wp
     * @return void
     */
    private function __construct(WP_Post $wp)
    {
        $this->wp = $wp;
    }

    /**
     * @return WP_Post
     */
    public function wp()
    {
        return $this->wp;
    }

    public function getTerms(...$args)
    {
        return call_user_func_array('get_the_terms', array_merge([ $this->wp->ID ], $args));
    }

    public function getCategories(...$args)
    {
        return call_user_func_array('get_the_category', array_merge([ $this->wp->ID ], $args));
    }

    public function getTags(...$args)
    {
        return call_user_func_array('get_the_tags', array_merge([ $this->wp->ID ], $args));
    }

    public function getPermalink(...$args)
    {
        return call_user_func_array('get_permalink', array_merge([ $this->wp->ID ], $args));
    }

    public function hasMeta(...$args)
    {
        return !empty(call_user_func_array('get_post_meta', array_merge([ $this->wp->ID ], $args)));
    }

    public function getMeta(...$args)
    {
        return call_user_func_array('get_post_meta', array_merge([ $this->wp->ID ], $args));
    }

    public function addMeta(...$args)
    {
        return call_user_func_array('add_post_meta', array_merge([ $this->wp->ID ], $args));
    }

    public function updateMeta(...$args)
    {
        return call_user_func_array('update_post_meta', array_merge([ $this->wp->ID ], $args));
    }

    public function hasThumbnail(...$args)
    {
        return call_user_func_array('has_post_thumbnail', array_merge([ $this->wp->ID ], $args));
    }
    public function getThumbnailURL(...$args)
    {
        return call_user_func_array('get_the_post_thumbnail_url', array_merge([ $this->wp->ID ], $args));
    }

    public function getThumbnailHtml(...$args)
    {
        return call_user_func_array('get_the_post_thumbnail', array_merge([ $this->wp->ID ], $args));
    }

    /*
     * Image
     * --------------------------------
     */

    private $hasImage = null;
    private $imageURLs = [];
    private $imageHTMLs = [];

    /**
     * @return void
     */
    private function parseImage()
    {
        if ($this->hasImage === null) {
            $match = preg_match_all('/<img.+?src=[\'"]([-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+)?[\'"].*?>/i', $this->wp->post_content, $matches);
            $this->hasImage = $match !== 0 && $match !== false;

            if ($this->hasImage) {
                $this->imageURLs = $matches[1];
                $this->imageHTMLs = $matches[0];
            }
        }
    }

    /**
     * @return boolean
     */
    public function hasImage()
    {
        $this->parseImage();

        return $this->hasImage;
    }

    /**
     * @return string[]
     */
    public function getImageURLs()
    {
        $this->parseImage();

        return $this->imageURLs;
    }

    /**
     * @return string[]
     */
    public function getImageHTMLs()
    {
        $this->parseImage();

        return $this->imageHTMLs;
    }

    /*
     * Attachment
     * --------------------------------
     */

    private $hasAttachmentImage = null;
    private $attachmentImageIds = [];

    /**
     * @return boolean
     */
    public function hasAttachmentImage()
    {
        return count($this->getAttachmentImageIds()) > 0;
    }

    /**
     * @return int[]
     */
    public function getAttachmentImageIds()
    {
        if ($this->hasImage()) {
            if (empty($this->imageURLs)) {
                return $this->attachmentImageIds;
            }

            $guids = [];
            foreach ($this->imageURLs as $source) {
                $guids[] = preg_replace('/(-e\d+)?(-\d+x\d+)?/i', '', $source);
            }

            global $wpdb;
            $fields = "'".implode("','", array_fill(0, count($guids), '%s'))."'";
            $sql = "SELECT ID FROM {$wpdb->posts} WHERE `guid` IN ({$fields}) ORDER BY FIELD(guid, {$fields})";
            $prepare = call_user_func_array([ $wpdb, 'prepare' ], array_merge([ $sql ], $guids, $guids));
            $results = $wpdb->get_results($prepare);

            if (empty($results)) {
                $this->attachmentImageIds = [];
            } else {
                $this->attachmentImageIds = $results;
            }
        }

        return $this->attachmentImageIds;
    }

    /**
     * @return string[]
     */
    public function getAttachmentImageURLs(...$args)
    {
        $urls = [];
        $ids = $this->getAttachmentImageIds();

        foreach ($ids as $id) {
            $return = call_user_func_array('wp_get_attachment_image_src', array_merge([ $id->ID ], $args));

            if ($return && isset($return[0])) {
                $urls[] = $return[0];
            }
        }

        return $urls;
    }

    /**
     * @return string[]
     */
    public function getAttachmentImageHTMLs(...$args)
    {
        $htmls = [];
        $ids = $this->getAttachmentImageIds();

        foreach ($ids as $id) {
            $return = call_user_func_array('wp_get_attachment_image', array_merge([ $id->ID ], $args));

            if ($return !== '') {
                $htmls[] = $return;
            }
        }

        return $htmls;
    }

    /*
     * Has if thumbnail
     * --------------------------------
     */

    /**
     * @return boolean
     */
    public function hasIfThumbnail()
    {
        return $this->hasThumbnail() || $this->hasAttachmentImage() || $this->hasImage();
    }

    /**
     * @return string
     */
    public function getIfThumbnail(...$args)
    {
        if ($this->hasThumbnail()) {
            return call_user_func_array([ $this, 'getThumbnailURL' ], $args);
        } elseif ($this->hasAttachmentImage()) {
            $return = call_user_func_array([ $this, 'getAttachmentImageURLs' ], $args);

            return isset($return[0]) ? $return[0] : '';
        } elseif ($this->hasImage()) {
            $return = $this->getImageURLs();

            return isset($return[0]) ? $return[0] : '';
        } else {
            return '';
        }
    }
}
