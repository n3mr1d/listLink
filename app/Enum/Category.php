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
}
