<?php namespace Ixyz\Landbaron\WP;

class Meta
{
    private $id = null;

    /**
     * @param int $id
     * @return self
     */
    public static function instance($id)
    {
        return new static($id);
    }

    /**
     * @param int $id
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * @param string $salt
     * @return boolean
     */
    public function verifyNonce($nonce, $salt)
    {
        return wp_verify_nonce($nonce, wp_create_nonce($salt));
    }

    /**
     * @param string $capability
     * @return boolean
     */
    public function verifyCapability($capability)
    {
        return current_user_can($capability, $this->id);
    }

    /**
     * @return boolean
     */
    public function verifyAutosave()
    {
        return defined('DOING_AUTOSAVE') && DOING_AUTOSAVE;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return int|boolean
     */
    public function saveUser($key, $value)
    {
        $meta = get_user_meta($this->id, $key, true);

        if ($value === '' || $value === null) {
            return delete_user_meta($this->id, $key, $meta);
        } elseif ($value !== $meta) {
            return update_user_meta($this->id, $key, $value);
        } elseif ($meta === '') {
            return add_user_meta($this->id, $key, $value);
        } else {
            return false;
        }
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return int|boolean
     */
    public function savePost($key, $value)
    {
        $meta = get_post_meta($this->id, $key, true);

        if ($value === '' || $value === null) {
            return delete_post_meta($this->id, $key, $meta);
        } elseif ($value !== $meta) {
            return update_post_meta($this->id, $key, $value);
        } elseif ($meta === '') {
            return add_post_meta($this->id, $key, $value);
        } else {
            return false;
        }
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return int|boolean
     */
    public function saveComment($key, $value)
    {
        $meta = get_comment_meta($this->id, $key, true);

        if ($value === '' || $value === null) {
            return delete_comment_meta($this->id, $key, $meta);
        } elseif ($value !== $meta) {
            return update_comment_meta($this->id, $key, $value);
        } elseif ($meta === '') {
            return add_comment_meta($this->id, $key, $value);
        } else {
            return false;
        }
    }
}
