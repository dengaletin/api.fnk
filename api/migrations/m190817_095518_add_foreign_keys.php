<?php

use yii\db\Migration;

/**
 * Class m190817_095518_add_foreign_keys
 */
class m190817_095518_add_foreign_keys extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->execute('
            DELETE msfo_news_companies
              FROM msfo_news_companies
         LEFT JOIN msfo_news
                ON msfo_news.id = msfo_news_companies.news_id
             WHERE msfo_news.id IS NULL
    
        ');

        $this->execute('
            DELETE msfo_news_companies
              FROM msfo_news_companies
         LEFT JOIN msfo_company
                ON msfo_company.id = msfo_news_companies.company_id
             WHERE msfo_company.id IS NULL
        ');

        $this->addForeignKey('msfo_news_companies_news_id', '{{%news_companies}}','news_id', '{{%news}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('msfo_news_companies_company_id', '{{%news_companies}}','company_id', '{{%company}}', 'id', 'CASCADE', 'CASCADE');

        $this->execute('
            DELETE msfo_parser_jobs
              FROM msfo_parser_jobs
         LEFT JOIN msfo_news
                ON msfo_news.id = msfo_parser_jobs.article_id
             WHERE msfo_news.id IS NULL
        ');

        $this->alterColumn('{{%parser_jobs}}', 'post_time', $this->timestamp()->defaultValue(null));

        $this->execute('
            UPDATE msfo_parser_jobs
               SET post_time = NULL 
             WHERE CAST(post_time AS CHAR(20)) = "0000-00-00 00:00:00"
        ');

        $this->addForeignKey('msfo_parser_jobs_article_id', '{{%parser_jobs}}','article_id', '{{%news}}', 'id', 'CASCADE', 'CASCADE');

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropForeignKey('msfo_news_companies_news_id', '{{%msfo_news_companies}}');
        $this->dropForeignKey('msfo_news_companies_company_id', '{{%msfo_news_companies}}');

        $this->dropForeignKey('msfo_parser_jobs_article_id', '{{%msfo_parser_jobs}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190817_095518_add_foreign_keys cannot be reverted.\n";

        return false;
    }
    */
}
