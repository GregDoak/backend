<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version00000000000000 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE genders (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', title VARCHAR(255) NOT NULL, created_on DATETIME NOT NULL, updated_on DATETIME DEFAULT NULL, INDEX IDX_831BA8BDDE12AB56 (created_by), INDEX IDX_831BA8BD16FE72E1 (updated_by), INDEX IDX_831BA8BD2B36786B (title), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE titles (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', title VARCHAR(255) NOT NULL, created_on DATETIME NOT NULL, updated_on DATETIME DEFAULT NULL, INDEX IDX_C14541A3DE12AB56 (created_by), INDEX IDX_C14541A316FE72E1 (updated_by), INDEX IDX_C14541A32B36786B (title), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE addresses (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', line1 VARCHAR(40) NOT NULL, line2 VARCHAR(40) DEFAULT NULL, line3 VARCHAR(40) DEFAULT NULL, city VARCHAR(40) NOT NULL, county VARCHAR(40) DEFAULT NULL, country VARCHAR(3) NOT NULL, postcode VARCHAR(10) NOT NULL, created_on DATETIME NOT NULL, updated_on DATETIME DEFAULT NULL, INDEX IDX_6FCA7516DE12AB56 (created_by), INDEX IDX_6FCA751616FE72E1 (updated_by), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE persons (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', title CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', gender CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', address CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', first_name VARCHAR(255) NOT NULL, middle_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) NOT NULL, date_of_birth DATETIME NOT NULL, created_on DATETIME NOT NULL, updated_on DATETIME DEFAULT NULL, INDEX IDX_A25CC7D32B36786B (title), INDEX IDX_A25CC7D3C7470A42 (gender), INDEX IDX_A25CC7D3D4E6F81 (address), INDEX IDX_A25CC7D3DE12AB56 (created_by), INDEX IDX_A25CC7D316FE72E1 (updated_by), INDEX IDX_A25CC7D3A9D1C132C808BA5A40F1B227 (first_name, last_name, date_of_birth), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE groups (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', title VARCHAR(255) NOT NULL, description VARCHAR(1024) NOT NULL, created_on DATETIME NOT NULL, updated_on DATETIME DEFAULT NULL, INDEX IDX_F06D3970DE12AB56 (created_by), INDEX IDX_F06D397016FE72E1 (updated_by), INDEX IDX_F06D39702B36786B (title), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE groups_roles (group_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', role_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_E79D4963FE54D947 (group_id), INDEX IDX_E79D4963D60322AC (role_id), PRIMARY KEY(group_id, role_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE jwt_refresh_tokens (id INT AUTO_INCREMENT NOT NULL, refresh_token VARCHAR(128) NOT NULL, username VARCHAR(255) NOT NULL, valid DATETIME NOT NULL, created_on DATETIME NOT NULL, updated_on DATETIME DEFAULT NULL, operating_system VARCHAR(255) NOT NULL, browser VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_4D2236BDC74F2195 (refresh_token), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE roles (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', title VARCHAR(255) NOT NULL, description VARCHAR(1024) NOT NULL, created_on DATETIME NOT NULL, updated_on DATETIME DEFAULT NULL, INDEX IDX_B63E2EC7DE12AB56 (created_by), INDEX IDX_B63E2EC716FE72E1 (updated_by), INDEX IDX_B63E2EC72B36786B (title), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', person CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', created_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', updated_by CHAR(36) DEFAULT NULL COMMENT \'(DC2Type:guid)\', username VARCHAR(255) NOT NULL, password VARCHAR(60) NOT NULL, password_created_on DATETIME NOT NULL, last_login DATETIME DEFAULT NULL, login_count INT NOT NULL, expired TINYINT(1) NOT NULL, enabled TINYINT(1) NOT NULL, created_on DATETIME NOT NULL, updated_on DATETIME DEFAULT NULL, INDEX IDX_1483A5E934DCD176 (person), INDEX IDX_1483A5E9DE12AB56 (created_by), INDEX IDX_1483A5E916FE72E1 (updated_by), INDEX IDX_1483A5E9F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users_groups (user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', group_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_FF8AB7E0A76ED395 (user_id), INDEX IDX_FF8AB7E0FE54D947 (group_id), PRIMARY KEY(user_id, group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users_roles (user_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', role_id CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', INDEX IDX_51498A8EA76ED395 (user_id), INDEX IDX_51498A8ED60322AC (role_id), PRIMARY KEY(user_id, role_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cron_jobs (id INT AUTO_INCREMENT NOT NULL, hostname VARCHAR(255) NOT NULL, pid INT NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cron_job_tasks (id INT AUTO_INCREMENT NOT NULL, cron_job_id INT DEFAULT NULL, command VARCHAR(1024) NOT NULL, start_date DATETIME NOT NULL, interval_period INT NOT NULL, interval_context VARCHAR(8) NOT NULL, priority INT NOT NULL, next_run DATETIME NOT NULL, last_run DATETIME DEFAULT NULL, created_on DATETIME NOT NULL, updated_on DATETIME DEFAULT NULL, active TINYINT(1) NOT NULL, INDEX IDX_5CD9453179099ED8 (cron_job_id), INDEX IDX_5CD9453179099ED8AEB7A5274B1EFC02 (cron_job_id, next_run, active), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cron_job_logs (id INT AUTO_INCREMENT NOT NULL, cron_job_id INT DEFAULT NULL, cron_job_task_id INT DEFAULT NULL, output LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:simple_array)\', exit_code INT NOT NULL, started_on DATETIME NOT NULL, ended_on DATETIME NOT NULL, INDEX IDX_C7B4589779099ED8 (cron_job_id), INDEX IDX_C7B458974E8536A0 (cron_job_task_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE audit_associations (id INT AUTO_INCREMENT NOT NULL, typ VARCHAR(128) NOT NULL, tbl VARCHAR(128) NOT NULL, label VARCHAR(255) DEFAULT NULL, fk VARCHAR(255) NOT NULL, class VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE audit_logs (id INT AUTO_INCREMENT NOT NULL, source_id INT NOT NULL, target_id INT DEFAULT NULL, blame_id INT DEFAULT NULL, action VARCHAR(12) NOT NULL, tbl VARCHAR(128) NOT NULL, diff JSON DEFAULT NULL COMMENT \'(DC2Type:json_array)\', logged_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_D62F2858953C1C61 (source_id), UNIQUE INDEX UNIQ_D62F2858158E0B66 (target_id), UNIQUE INDEX UNIQ_D62F28588C082A2E (blame_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE genders ADD CONSTRAINT FK_831BA8BDDE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE genders ADD CONSTRAINT FK_831BA8BD16FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE titles ADD CONSTRAINT FK_C14541A3DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE titles ADD CONSTRAINT FK_C14541A316FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE addresses ADD CONSTRAINT FK_6FCA7516DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE addresses ADD CONSTRAINT FK_6FCA751616FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE persons ADD CONSTRAINT FK_A25CC7D32B36786B FOREIGN KEY (title) REFERENCES titles (id)');
        $this->addSql('ALTER TABLE persons ADD CONSTRAINT FK_A25CC7D3C7470A42 FOREIGN KEY (gender) REFERENCES genders (id)');
        $this->addSql('ALTER TABLE persons ADD CONSTRAINT FK_A25CC7D3D4E6F81 FOREIGN KEY (address) REFERENCES addresses (id)');
        $this->addSql('ALTER TABLE persons ADD CONSTRAINT FK_A25CC7D3DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE persons ADD CONSTRAINT FK_A25CC7D316FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE groups ADD CONSTRAINT FK_F06D3970DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE groups ADD CONSTRAINT FK_F06D397016FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE groups_roles ADD CONSTRAINT FK_E79D4963FE54D947 FOREIGN KEY (group_id) REFERENCES groups (id)');
        $this->addSql('ALTER TABLE groups_roles ADD CONSTRAINT FK_E79D4963D60322AC FOREIGN KEY (role_id) REFERENCES roles (id)');
        $this->addSql('ALTER TABLE roles ADD CONSTRAINT FK_B63E2EC7DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE roles ADD CONSTRAINT FK_B63E2EC716FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E934DCD176 FOREIGN KEY (person) REFERENCES persons (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9DE12AB56 FOREIGN KEY (created_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E916FE72E1 FOREIGN KEY (updated_by) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users_groups ADD CONSTRAINT FK_FF8AB7E0A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users_groups ADD CONSTRAINT FK_FF8AB7E0FE54D947 FOREIGN KEY (group_id) REFERENCES groups (id)');
        $this->addSql('ALTER TABLE users_roles ADD CONSTRAINT FK_51498A8EA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users_roles ADD CONSTRAINT FK_51498A8ED60322AC FOREIGN KEY (role_id) REFERENCES roles (id)');
        $this->addSql('ALTER TABLE cron_job_tasks ADD CONSTRAINT FK_5CD9453179099ED8 FOREIGN KEY (cron_job_id) REFERENCES cron_jobs (id)');
        $this->addSql('ALTER TABLE cron_job_logs ADD CONSTRAINT FK_C7B4589779099ED8 FOREIGN KEY (cron_job_id) REFERENCES cron_jobs (id)');
        $this->addSql('ALTER TABLE cron_job_logs ADD CONSTRAINT FK_C7B458974E8536A0 FOREIGN KEY (cron_job_task_id) REFERENCES cron_job_tasks (id)');
        $this->addSql('ALTER TABLE audit_logs ADD CONSTRAINT FK_D62F2858953C1C61 FOREIGN KEY (source_id) REFERENCES audit_associations (id)');
        $this->addSql('ALTER TABLE audit_logs ADD CONSTRAINT FK_D62F2858158E0B66 FOREIGN KEY (target_id) REFERENCES audit_associations (id)');
        $this->addSql('ALTER TABLE audit_logs ADD CONSTRAINT FK_D62F28588C082A2E FOREIGN KEY (blame_id) REFERENCES audit_associations (id)');
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql',
            'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE persons DROP FOREIGN KEY FK_A25CC7D3C7470A42');
        $this->addSql('ALTER TABLE persons DROP FOREIGN KEY FK_A25CC7D32B36786B');
        $this->addSql('ALTER TABLE persons DROP FOREIGN KEY FK_A25CC7D3D4E6F81');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E934DCD176');
        $this->addSql('ALTER TABLE groups_roles DROP FOREIGN KEY FK_E79D4963FE54D947');
        $this->addSql('ALTER TABLE users_groups DROP FOREIGN KEY FK_FF8AB7E0FE54D947');
        $this->addSql('ALTER TABLE groups_roles DROP FOREIGN KEY FK_E79D4963D60322AC');
        $this->addSql('ALTER TABLE users_roles DROP FOREIGN KEY FK_51498A8ED60322AC');
        $this->addSql('ALTER TABLE genders DROP FOREIGN KEY FK_831BA8BDDE12AB56');
        $this->addSql('ALTER TABLE genders DROP FOREIGN KEY FK_831BA8BD16FE72E1');
        $this->addSql('ALTER TABLE titles DROP FOREIGN KEY FK_C14541A3DE12AB56');
        $this->addSql('ALTER TABLE titles DROP FOREIGN KEY FK_C14541A316FE72E1');
        $this->addSql('ALTER TABLE addresses DROP FOREIGN KEY FK_6FCA7516DE12AB56');
        $this->addSql('ALTER TABLE addresses DROP FOREIGN KEY FK_6FCA751616FE72E1');
        $this->addSql('ALTER TABLE persons DROP FOREIGN KEY FK_A25CC7D3DE12AB56');
        $this->addSql('ALTER TABLE persons DROP FOREIGN KEY FK_A25CC7D316FE72E1');
        $this->addSql('ALTER TABLE groups DROP FOREIGN KEY FK_F06D3970DE12AB56');
        $this->addSql('ALTER TABLE groups DROP FOREIGN KEY FK_F06D397016FE72E1');
        $this->addSql('ALTER TABLE roles DROP FOREIGN KEY FK_B63E2EC7DE12AB56');
        $this->addSql('ALTER TABLE roles DROP FOREIGN KEY FK_B63E2EC716FE72E1');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9DE12AB56');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E916FE72E1');
        $this->addSql('ALTER TABLE users_groups DROP FOREIGN KEY FK_FF8AB7E0A76ED395');
        $this->addSql('ALTER TABLE users_roles DROP FOREIGN KEY FK_51498A8EA76ED395');
        $this->addSql('ALTER TABLE cron_job_tasks DROP FOREIGN KEY FK_5CD9453179099ED8');
        $this->addSql('ALTER TABLE cron_job_logs DROP FOREIGN KEY FK_C7B4589779099ED8');
        $this->addSql('ALTER TABLE cron_job_logs DROP FOREIGN KEY FK_C7B458974E8536A0');
        $this->addSql('ALTER TABLE audit_logs DROP FOREIGN KEY FK_D62F2858953C1C61');
        $this->addSql('ALTER TABLE audit_logs DROP FOREIGN KEY FK_D62F2858158E0B66');
        $this->addSql('ALTER TABLE audit_logs DROP FOREIGN KEY FK_D62F28588C082A2E');
        $this->addSql('DROP TABLE genders');
        $this->addSql('DROP TABLE titles');
        $this->addSql('DROP TABLE addresses');
        $this->addSql('DROP TABLE persons');
        $this->addSql('DROP TABLE groups');
        $this->addSql('DROP TABLE groups_roles');
        $this->addSql('DROP TABLE jwt_refresh_tokens');
        $this->addSql('DROP TABLE roles');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE users_groups');
        $this->addSql('DROP TABLE users_roles');
        $this->addSql('DROP TABLE cron_jobs');
        $this->addSql('DROP TABLE cron_job_tasks');
        $this->addSql('DROP TABLE cron_job_logs');
        $this->addSql('DROP TABLE audit_associations');
        $this->addSql('DROP TABLE audit_logs');
    }
}
