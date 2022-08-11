<?php
namespace App\Util;

use App\Entity\AbstractPost;
use App\Entity\Topic;
use App\Entity\User;
use Exception;

class ForumUtil
{

    private function __construct()
    {
    }

    /**
     * @param Topic[] $topics
     * @return AbstractPost[]
     * returns <Topic id, AbstractPost>
     */
    public static function getLastPostByTopics(array $topics): array
    {
        //TODO comments or topics

        $lastPosts = [];
        foreach ($topics as $topic) {
            $post = $topic->getComments()->getValues();
            $count = $topic->getComments()->count();
            if ($count > 0) {
                $lastPosts[$topic->getId()] = $post[0];
            } else {
                $lastPosts[$topic->getId()] = $topic;
            }
        }

        return $lastPosts;
    }

    /**
     * @param AbstractPost[] $array
     * @return User[] $res
     * @throws Exception
     */
    public static function getUsersFromTopics(array $array): array
    {
        $res = [];
        foreach ($array as $element) {
            if ($element instanceof AbstractPost) {
                $res[] = $element->getAuthor();
            } else {
                throw new Exception("It is not an instance of " . Topic::class);
            }
        }
        return $res;
    }

    /**
     * @param AbstractPost[] $posts
     * @return User[]
     */
    public static function fetchUsersFromAbstractPost(array $posts): array
    {
        $authors = [];

        foreach ($posts as $post) {
            if (!empty($post)) {
                $authors[] = $post->getAuthor();
            }
        }
        return $authors;
    }
}
