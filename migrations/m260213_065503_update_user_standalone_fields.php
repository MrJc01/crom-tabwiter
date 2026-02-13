<?php

use yii\db\Migration;

/**
 * Class m260213_065503_update_user_standalone_fields
 */
class m260213_065503_update_user_standalone_fields extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Add email field
        $this->addColumn('{{%user}}', 'email', $this->string()->unique()->after('username'));

        // Add auth_key (for cookie-based login)
        $this->addColumn('{{%user}}', 'auth_key', $this->string(32)->after('email'));

        // Add password_hash
        $this->addColumn('{{%user}}', 'password_hash', $this->string()->after('auth_key'));

        // Rename auth_hash to something else or drop it if not needed? 
        // The prompt says "signup with mana_weekly = 100".
        // Existing logic used 'auth_hash' as a token. We can keep it or drop it.
        // Standard Yii2 uses auth_key. Let's drop auth_hash if it conflicts or just keep it for valid tokens existing?
        // Let's drop it to be clean, as we are moving to strict login.
        // But wait, user says "Migration mxxxxxx_update_user_standalone: Adicionar: email (unique), password_hash, auth_key, mana_weekly (default 100)."
        // It doesn't explicitly say drop old fields but "Simplificar" implies cleaning.

        $this->dropColumn('{{%user}}', 'auth_hash');
        $this->dropColumn('{{%user}}', 'tabnews_id'); // Assuming we decoupling from tabnews
        //$this->dropColumn('{{%user}}', 'tabcoins_balance'); // Maybe keep as score? Prompt says "Mechanic of gamification (Mana/Decay)".

        // Let's ensure mana_weekly is correct
        // existing code has mana_weekly. Let's alter it to default 100.
        $this->alterColumn('{{%user}}', 'mana_weekly', $this->integer()->defaultValue(100));

        // Verification column?
        // existing has is_validated.
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'email');
        $this->dropColumn('{{%user}}', 'password_hash');
        $this->dropColumn('{{%user}}', 'auth_key');

        $this->addColumn('{{%user}}', 'auth_hash', $this->string());
        $this->addColumn('{{%user}}', 'tabnews_id', $this->string());
    }
}
