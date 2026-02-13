<?php

use yii\db\Migration;

/**
 * Class m000000_000001_create_user_table
 *
 * Basic user table for TabWiter network
 */
class m000000_000001_create_user_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'auth_hash' => $this->string()->notNull(),
            'tabnews_id' => $this->string()->null(),
            'tabcoins_balance' => $this->integer()->notNull()->defaultValue(0),
            'mana_weekly' => $this->integer()->notNull()->defaultValue(0),
            'is_validated' => $this->boolean()->notNull()->defaultValue(false),
            'last_active_at' => $this->integer()->null(),
            'created_at' => $this->integer()->notNull(),
        ]);

        // index for fast lookup by auth_hash and tabnews_id
        $this->createIndex('idx-user-auth_hash', '{{%user}}', 'auth_hash');
        $this->createIndex('idx-user-tabnews_id', '{{%user}}', 'tabnews_id');
    }

    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}
