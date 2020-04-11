<?php


namespace App\Utility;


use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;

class MercureSender {
    private $publisher;

    public const METHOD_DELETE = 'delete';
    public const METHOD_ADD = 'add';
    public const METHOD_PATCH = 'patch';

    private $topic_url;


    /**
     * MercureSender constructor.
     * @param PublisherInterface $publisher
     * @param string $topic_url
     */
    public function __construct(PublisherInterface $publisher, string $topic_url) {
        $this->publisher = $publisher;
        $this->topic_url = $topic_url;
    }

    /**
     * @param string $object
     * @param string $method
     * @param array $content
     */
    public function sendUpdate(string $object, string $method, array $content){
        $publisher = $this->publisher;

        $data['content'] = $content;
        $data['object'] = $object;
        $data['method'] = $method;
        $data['time'] = date("Y-m-d h:i:sa");

        $update = new Update(
            $this->topic_url,
            json_encode($data)
        );

        $publisher($update);
    }
}