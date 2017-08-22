<?php namespace Ixyz\Landbaron\WP;

class Option
{
    /**
     * @return Option
     */
    public static function instance()
    {
        return new static;
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
        return current_user_can($this->capability, $this->id);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return int|boolean
     */
    public function save($key, $value)
    {
        $option = get_option($key);

        if ($value === '' || $value === null) {
            return delete_option($key, $option);
        } elseif ($value !== $option) {
            return update_option($key, $value);
        } elseif ($option === false) {
            return add_option($key, $value);
        } else {
            return false;
        }
    }
}
