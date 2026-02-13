<?php

use yii\db\Migration;

/**
 * Creates post_tag and vote tables for hashtag support and voting.
 */
class m000000_000003_create_tags_and_votes extends Migration
{
    public function safeUp()
    {
        // Hashtag relation table
        $this->createTable('{{%post_tag}}', [
            'id' => $this->primaryKey(),
            'post_id' => $this->integer()->notNull(),
            'tag' => $this->string(100)->notNull(),
        ]);

        $this->createIndex('idx-post_tag-post_id', '{{%post_tag}}', 'post_id');
        $this->createIndex('idx-post_tag-tag', '{{%post_tag}}', 'tag');

        // Vote table (prevents double voting)
        $this->createTable('{{%vote}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'post_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->createIndex('idx-vote-unique', '{{%vote}}', ['user_id', 'post_id'], true);
    }

    public function safeDown()
    {
        $this->dropTable('{{%vote}}');
        $this->dropTable('{{%post_tag}}');
    }
}
