<?php

use yii\db\Migration;

/**
 * Class m000000_000002_create_post_table
 */
class m000000_000002_create_post_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%post}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'content' => $this->text()->notNull(),
            'points' => $this->integer()->notNull()->defaultValue(1),
            'is_tabnews_sync' => $this->boolean()->notNull()->defaultValue(false),
            'created_at' => $this->integer()->notNull(),
        ]);

        // SQLite doesn't support foreign keys via migration builder;
        // if needed the application layer should enforce referential integrity.
        // alternatively enable PRAGMA foreign_keys in db connection.

    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-post-user_id', '{{%post}}');
        $this->dropTable('{{%post}}');
    }
}
