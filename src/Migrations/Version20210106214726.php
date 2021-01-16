<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210106214726 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category ALTER id TYPE VARCHAR');
        $this->addSql('ALTER TABLE category ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE category ALTER status TYPE VARCHAR');
        $this->addSql('ALTER TABLE category ALTER status DROP DEFAULT');
        $this->addSql('ALTER TABLE course ALTER id TYPE VARCHAR');
        $this->addSql('ALTER TABLE course ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE course ALTER teacher_id TYPE VARCHAR');
        $this->addSql('ALTER TABLE course ALTER teacher_id DROP DEFAULT');
        $this->addSql('ALTER TABLE course ALTER category_id TYPE VARCHAR');
        $this->addSql('ALTER TABLE course ALTER category_id DROP DEFAULT');
        $this->addSql('ALTER TABLE course ALTER status TYPE VARCHAR');
        $this->addSql('ALTER TABLE course ALTER status DROP DEFAULT');
        $this->addSql('ALTER TABLE course_permission ALTER id TYPE VARCHAR');
        $this->addSql('ALTER TABLE course_permission ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE course_permission ALTER course_id TYPE VARCHAR');
        $this->addSql('ALTER TABLE course_permission ALTER course_id DROP DEFAULT');
        $this->addSql('ALTER TABLE course_permission ALTER student_id TYPE VARCHAR');
        $this->addSql('ALTER TABLE course_permission ALTER student_id DROP DEFAULT');
        $this->addSql('ALTER TABLE course_permission ALTER status TYPE VARCHAR');
        $this->addSql('ALTER TABLE course_permission ALTER status DROP DEFAULT');
        $this->addSql('ALTER TABLE refresh_tokens ALTER refresh_token TYPE VARCHAR');
        $this->addSql('ALTER TABLE refresh_tokens ALTER refresh_token DROP DEFAULT');
        $this->addSql('ALTER TABLE refresh_tokens ALTER user_id TYPE VARCHAR');
        $this->addSql('ALTER TABLE refresh_tokens ALTER user_id DROP DEFAULT');
        $this->addSql('ALTER TABLE resource ALTER id TYPE VARCHAR');
        $this->addSql('ALTER TABLE resource ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE resource ALTER unit_id TYPE VARCHAR');
        $this->addSql('ALTER TABLE resource ALTER unit_id DROP DEFAULT');
        $this->addSql('ALTER TABLE resource ALTER status TYPE VARCHAR');
        $this->addSql('ALTER TABLE resource ALTER status DROP DEFAULT');
        $this->addSql('ALTER TABLE resource_student_answer ALTER id TYPE VARCHAR');
        $this->addSql('ALTER TABLE resource_student_answer ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE resource_student_answer ALTER resource_id TYPE VARCHAR');
        $this->addSql('ALTER TABLE resource_student_answer ALTER resource_id DROP DEFAULT');
        $this->addSql('ALTER TABLE resource_student_answer ALTER student_id TYPE VARCHAR');
        $this->addSql('ALTER TABLE resource_student_answer ALTER student_id DROP DEFAULT');
        $this->addSql('ALTER TABLE resource_student_answer ALTER status TYPE VARCHAR');
        $this->addSql('ALTER TABLE resource_student_answer ALTER status DROP DEFAULT');
        $this->addSql('ALTER TABLE test_resource ALTER id TYPE VARCHAR');
        $this->addSql('ALTER TABLE test_resource ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE test_resource ALTER questions TYPE JSON');
        $this->addSql('ALTER TABLE test_resource ALTER questions DROP DEFAULT');
        $this->addSql('ALTER TABLE test_resource_student_answer ALTER id TYPE VARCHAR');
        $this->addSql('ALTER TABLE test_resource_student_answer ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE test_resource_student_answer ALTER assumptions TYPE JSON');
        $this->addSql('ALTER TABLE test_resource_student_answer ALTER assumptions DROP DEFAULT');
        $this->addSql('ALTER TABLE unit ALTER id TYPE VARCHAR');
        $this->addSql('ALTER TABLE unit ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE unit ALTER course_id TYPE VARCHAR');
        $this->addSql('ALTER TABLE unit ALTER course_id DROP DEFAULT');
        $this->addSql('ALTER TABLE unit ALTER status TYPE VARCHAR');
        $this->addSql('ALTER TABLE unit ALTER status DROP DEFAULT');
        $this->addSql('ALTER TABLE user_account ALTER id TYPE VARCHAR');
        $this->addSql('ALTER TABLE user_account ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE user_account ALTER email TYPE VARCHAR');
        $this->addSql('ALTER TABLE user_account ALTER email DROP DEFAULT');
        $this->addSql('ALTER TABLE user_account ALTER first_name TYPE VARCHAR');
        $this->addSql('ALTER TABLE user_account ALTER first_name DROP DEFAULT');
        $this->addSql('ALTER TABLE user_account ALTER last_name TYPE VARCHAR');
        $this->addSql('ALTER TABLE user_account ALTER last_name DROP DEFAULT');
        $this->addSql('ALTER TABLE user_account ALTER roles TYPE VARCHAR');
        $this->addSql('ALTER TABLE user_account ALTER roles DROP DEFAULT');
        $this->addSql('ALTER TABLE user_account ALTER password TYPE VARCHAR');
        $this->addSql('ALTER TABLE user_account ALTER password DROP DEFAULT');
        $this->addSql('ALTER TABLE user_account ALTER confirmation_token TYPE VARCHAR');
        $this->addSql('ALTER TABLE user_account ALTER confirmation_token DROP DEFAULT');
        $this->addSql('ALTER TABLE user_connection ALTER student_id TYPE VARCHAR');
        $this->addSql('ALTER TABLE user_connection ALTER student_id DROP DEFAULT');
        $this->addSql('ALTER TABLE user_connection ALTER teacher_id TYPE VARCHAR');
        $this->addSql('ALTER TABLE user_connection ALTER teacher_id DROP DEFAULT');
        $this->addSql('ALTER TABLE user_connection ALTER state TYPE VARCHAR');
        $this->addSql('ALTER TABLE user_connection ALTER state DROP DEFAULT');
        $this->addSql('ALTER TABLE user_connection ALTER specifier_id TYPE VARCHAR');
        $this->addSql('ALTER TABLE user_connection ALTER specifier_id DROP DEFAULT');
        $this->addSql('ALTER TABLE video_resource ALTER id TYPE VARCHAR');
        $this->addSql('ALTER TABLE video_resource ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE video_resource_student_answer ALTER id TYPE VARCHAR');
        $this->addSql('ALTER TABLE video_resource_student_answer ALTER id DROP DEFAULT');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE user_connection ALTER student_id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_connection ALTER student_id DROP DEFAULT');
        $this->addSql('ALTER TABLE user_connection ALTER teacher_id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_connection ALTER teacher_id DROP DEFAULT');
        $this->addSql('ALTER TABLE user_connection ALTER state TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_connection ALTER state DROP DEFAULT');
        $this->addSql('ALTER TABLE user_connection ALTER specifier_id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_connection ALTER specifier_id DROP DEFAULT');
        $this->addSql('ALTER TABLE user_account ALTER id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_account ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE user_account ALTER email TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_account ALTER email DROP DEFAULT');
        $this->addSql('ALTER TABLE user_account ALTER first_name TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_account ALTER first_name DROP DEFAULT');
        $this->addSql('ALTER TABLE user_account ALTER last_name TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_account ALTER last_name DROP DEFAULT');
        $this->addSql('ALTER TABLE user_account ALTER roles TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_account ALTER roles DROP DEFAULT');
        $this->addSql('ALTER TABLE user_account ALTER password TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_account ALTER password DROP DEFAULT');
        $this->addSql('ALTER TABLE user_account ALTER confirmation_token TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE user_account ALTER confirmation_token DROP DEFAULT');
        $this->addSql('ALTER TABLE refresh_tokens ALTER refresh_token TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE refresh_tokens ALTER refresh_token DROP DEFAULT');
        $this->addSql('ALTER TABLE refresh_tokens ALTER user_id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE refresh_tokens ALTER user_id DROP DEFAULT');
        $this->addSql('ALTER TABLE resource ALTER id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE resource ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE resource ALTER unit_id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE resource ALTER unit_id DROP DEFAULT');
        $this->addSql('ALTER TABLE resource ALTER status TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE resource ALTER status DROP DEFAULT');
        $this->addSql('ALTER TABLE category ALTER id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE category ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE category ALTER status TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE category ALTER status DROP DEFAULT');
        $this->addSql('ALTER TABLE course_permission ALTER id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE course_permission ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE course_permission ALTER course_id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE course_permission ALTER course_id DROP DEFAULT');
        $this->addSql('ALTER TABLE course_permission ALTER student_id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE course_permission ALTER student_id DROP DEFAULT');
        $this->addSql('ALTER TABLE course_permission ALTER status TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE course_permission ALTER status DROP DEFAULT');
        $this->addSql('ALTER TABLE course ALTER id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE course ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE course ALTER teacher_id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE course ALTER teacher_id DROP DEFAULT');
        $this->addSql('ALTER TABLE course ALTER category_id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE course ALTER category_id DROP DEFAULT');
        $this->addSql('ALTER TABLE course ALTER status TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE course ALTER status DROP DEFAULT');
        $this->addSql('ALTER TABLE unit ALTER id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE unit ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE unit ALTER course_id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE unit ALTER course_id DROP DEFAULT');
        $this->addSql('ALTER TABLE unit ALTER status TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE unit ALTER status DROP DEFAULT');
        $this->addSql('ALTER TABLE test_resource_student_answer ALTER id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE test_resource_student_answer ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE test_resource_student_answer ALTER assumptions TYPE JSON');
        $this->addSql('ALTER TABLE test_resource_student_answer ALTER assumptions DROP DEFAULT');
        $this->addSql('ALTER TABLE test_resource ALTER id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE test_resource ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE test_resource ALTER questions TYPE JSON');
        $this->addSql('ALTER TABLE test_resource ALTER questions DROP DEFAULT');
        $this->addSql('ALTER TABLE video_resource_student_answer ALTER id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE video_resource_student_answer ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE resource_student_answer ALTER id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE resource_student_answer ALTER id DROP DEFAULT');
        $this->addSql('ALTER TABLE resource_student_answer ALTER resource_id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE resource_student_answer ALTER resource_id DROP DEFAULT');
        $this->addSql('ALTER TABLE resource_student_answer ALTER student_id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE resource_student_answer ALTER student_id DROP DEFAULT');
        $this->addSql('ALTER TABLE resource_student_answer ALTER status TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE resource_student_answer ALTER status DROP DEFAULT');
        $this->addSql('ALTER TABLE video_resource ALTER id TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE video_resource ALTER id DROP DEFAULT');
    }
}
