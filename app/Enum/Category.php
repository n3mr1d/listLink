<?php

namespace App\Enum;

enum Category: string
{
    case PRIVACY_SECURITY = 'privacy_security';
    case ANONYMITY_TOOLS = 'anonymity_tools';
    case EMAIL_MESSAGING = 'email_messaging';
    case SOCIAL_NETWORKS = 'social_networks';
    case FORUMS_COMMUNITIES = 'forums_communities';
    case BLOGS_NEWS = 'blogs_news';
    case WHISTLEBLOWING = 'whistleblowing';
    case FILE_SHARING = 'file_sharing';
    case HOSTING_SERVICES = 'hosting_services';
    case SOFTWARE_DEVELOPMENT = 'software_development';
    case CRYPTO_BLOCKCHAIN = 'crypto_blockchain';
    case BOOKS_ARCHIVES = 'books_archives';
    case EROTIC_PORN = 'erotic_porn';
    case GENERAL_KNOWLEDGE = 'general_knowledge';
    case OTHER = 'other';


    public function label(): string
    {
        return match ($this) {
            self::PRIVACY_SECURITY => 'Privacy & Security',
            self::ANONYMITY_TOOLS => 'Anonymity Tools',
            self::EMAIL_MESSAGING => 'Email Messaging',
            self::SOCIAL_NETWORKS => 'Social Networks',
            self::FORUMS_COMMUNITIES => 'Forums & Communities',
            self::BLOGS_NEWS => 'Blogs & News',
            self::WHISTLEBLOWING => 'Whistleblowing',
            self::FILE_SHARING => 'File Sharing',
            self::HOSTING_SERVICES => 'Hosting Services',
            self::SOFTWARE_DEVELOPMENT => 'Software Development',
            self::CRYPTO_BLOCKCHAIN => 'Crypto & Blockchain',
            self::BOOKS_ARCHIVES => 'Books & Archives',
            self::EROTIC_PORN => 'Erotic Porn',
            self::GENERAL_KNOWLEDGE => 'General Knowledge',
            self::OTHER => 'Other',

        };
    }

}
