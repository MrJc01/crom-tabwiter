<?php

namespace tests\unit\models;

use app\models\Post;
use app\models\User;
use Yii;

class PostTest extends \Codeception\Test\Unit
{
    /**
     * Test that applyDecay decrements post points by 1.
     */
    public function testApplyDecayDecrementsPoints()
    {
        // Create a test user
        $user = new User();
        $user->username = 'test_decay_' . time();
        $user->auth_hash = Yii::$app->security->generateRandomString();
        $user->save(false);

        // Create a test post with 5 points
        $post = new Post();
        $post->user_id = $user->id;
        $post->content = 'Test post for decay';
        $post->points = 5;
        $post->created_at = time();
        $post->is_tabnews_sync = false;
        $post->save(false);

        $postId = $post->id;

        // Apply decay
        $result = $post->applyDecay();
        $this->assertTrue($result);

        // Reload and verify
        $post->refresh();
        $this->assertEquals(4, $post->points);

        // Clean up
        Post::deleteAll(['id' => $postId]);
        User::deleteAll(['id' => $user->id]);
    }

    /**
     * Test that applyDecay deletes posts at the death threshold.
     */
    public function testApplyDecayDeletesAtThreshold()
    {
        $user = new User();
        $user->username = 'test_death_' . time();
        $user->auth_hash = Yii::$app->security->generateRandomString();
        $user->save(false);

        $post = new Post();
        $post->user_id = $user->id;
        $post->content = 'This post will die';
        $post->points = -9; // One decay away from -10 threshold
        $post->created_at = time();
        $post->is_tabnews_sync = false;
        $post->save(false);

        $postId = $post->id;

        // Apply decay — should trigger deletion
        $post->applyDecay();

        // Verify post no longer exists
        $this->assertNull(Post::findOne($postId));

        // Clean up
        User::deleteAll(['id' => $user->id]);
    }

    /**
     * Test life expectancy calculation.
     */
    public function testLifeExpectancy()
    {
        $post = new Post();
        $post->points = 5;
        // With death at -10 and decay of 1/day: 5 - (-10) = 15 days
        $this->assertEquals(15, $post->getLifeExpectancy());

        $post->points = 0;
        // 0 - (-10) = 10 days
        $this->assertEquals(10, $post->getLifeExpectancy());

        $post->points = -9;
        // -9 - (-10) = 1 day
        $this->assertEquals(1, $post->getLifeExpectancy());
    }

    /**
     * Test life color mapping.
     */
    public function testLifeColor()
    {
        $post = new Post();

        $post->points = 10;
        $this->assertEquals('green', $post->getLifeColor());

        $post->points = 3;
        $this->assertEquals('gray', $post->getLifeColor());

        $post->points = -6;
        $this->assertEquals('red', $post->getLifeColor());
    }
}
