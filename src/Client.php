<?php

namespace DiscordWebhooksPHP;

class Client
{
    protected $url = null;
    protected $username = null;
    protected $avatar = null;
    protected $message = null;
    protected $isEmbed = false;
    protected $embedData = array();

    /**
     * Client constructor.
     * @param string $url
     */
    public function __construct($url) {
        $this->url = $url;
    }

    /**
     * @param string $username
     */
    public function setUsername($username) {
        $this->username = $username;
    }

    /**
     * @param string $avatar
     */
    public function setAvatar($avatar) {
        $this->avatar = $avatar;
    }

    /**
     * @param string $message
     */
    public function setMessage($message) {
        $this->message = $message;
    }

    /**
     * @param array $embedData
     * @throws DiscordException
     */
    public function setEmbedData($embedData){
        if(!is_array($embedData))
            throw new DiscordException('EmbedData is not array');
        $this->isEmbed = true;
        $this->embedData = array($embedData);
    }


    /**
     * @return bool
     * @throws DiscordException
     */
    public function send() {
        if(strlen($this->message) > 2000)
            throw new DiscordException('Message maximum 2000 characters');

        $message = $this->message;
        $url = $this->url;

        $data = array(
            'content' => $message,
            'username' => $this->username,
            'avatar_url' => $this->avatar,
        );

        if($this->isEmbed) {
            $data['type'] = 'rich';
            $data['embeds'] = $this->embedData;
        }else{
            if($this->message == null)
                throw new DiscordException('message field should not be empty');
        }

        $data_string = json_encode($data);

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

        $output = curl_exec($curl);
        $output = json_decode($output, true);

        if (curl_getinfo($curl, CURLINFO_HTTP_CODE) != 204) {
            throw new DiscordException($output['message']);
        }

        curl_close($curl);
        return true;
    }
}