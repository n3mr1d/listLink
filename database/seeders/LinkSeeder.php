<?php

namespace Database\Seeders;

use App\Models\Link;
use App\Enum\Category;
use App\Enum\Status;
use App\Enum\UptimeStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LinkSeeder extends Seeder
{
    public function run(): void
    {
        $links = [
            // ANONYMITY_TOOLS
            [
                'title' => 'Ahmia.fi',
                'url' => 'http://juhanurmihxlp77nkq76byazcldy2hlmovfu2epvl5ankdibsot4csyd.onion/',
                'description' => 'Clearnet search engine for Tor Hidden Services.',
                'category' => Category::ANONYMITY_TOOLS,
            ],
            [
                'title' => 'DuckDuckGo',
                'url' => 'https://duckduckgogg42xjoc72x3sjasowoarfbgcmvfimaftt6twagswzczad.onion/',
                'description' => 'A Hidden Service that searches the clearnet.',
                'category' => Category::ANONYMITY_TOOLS,
            ],
            [
                'title' => 'Torch',
                'url' => 'http://xmh57jrknzkhv6y3ls3ubitzfqnkrwxhopf5aygthi7d6rplyvk3noyd.onion/',
                'description' => 'Tor Search Engine. Claims to index around 1.1 Million pages.',
                'category' => Category::ANONYMITY_TOOLS,
            ],

            // PRIVACY_SECURITY
            [
                'title' => 'Cryptostorm',
                'url' => 'http://stormwayszuh4juycoy4kwoww5gvcu2c4tdtpkup667pdwe4qenzwayd.onion/',
                'description' => "Tor's own secure VPN service.",
                'category' => Category::PRIVACY_SECURITY,
            ],
            [
                'title' => 'Mullvad VPN',
                'url' => 'http://o54hon2e2vj6c7m3aqqu6uyece65by3vgoxxhlqlsvkmacw6a7m7kiad.onion/',
                'description' => 'Free the internet. Bitcoin accepted.',
                'category' => Category::PRIVACY_SECURITY,
            ],
            [
                'title' => 'Have I Been Pwned?',
                'url' => 'http://pwneduufdnxe3dvfwvnzlv5rt4xbfaepc6rw5pajekuwqxe32uivkyyd.onion/',
                'description' => 'Check if your email address is in a data breach.',
                'category' => Category::PRIVACY_SECURITY,
            ],

            // EMAIL_MESSAGING
            [
                'title' => 'Mail2Tor',
                'url' => 'http://mail2torjgmxgexntbrmhvgluavhj7ouul5yar6ylbvjkxwqf6ixkwyd.onion/',
                'description' => 'Mail2Tor is a free anonymous e-mail service made to protect your privacy.',
                'category' => Category::EMAIL_MESSAGING,
            ],
            [
                'title' => 'Protonmail',
                'url' => 'http://protonmailrmez3lotccipshtkleegetolb73fuirgj7r4o4vfu7ozyd.onion/',
                'description' => 'Swiss based e-mail service, encrypts e-mails locally on your browser.',
                'category' => Category::EMAIL_MESSAGING,
            ],
            [
                'title' => 'SimpleX chat',
                'url' => 'http://isdb4l77sjqoy2qq7ipum6x3at6hyn3jmxfx4zdhc72ufbmuq4ilwkqd.onion/',
                'description' => "Private and secure messenger without any user ID's.",
                'category' => Category::EMAIL_MESSAGING,
            ],

            // SOCIAL_NETWORKS
            [
                'title' => 'Facebook',
                'url' => 'http://facebookwkhpilnemxj7asaniu7vnjjbiltxjqhye3mhbshg7kx5tfyd.onion/',
                'description' => "Facebook's official Onion version.",
                'category' => Category::SOCIAL_NETWORKS,
            ],
            [
                'title' => 'Twitter (Nitter)',
                'url' => 'http://nitter7bryz3jv7e3uekphigvmoyoem4al3fynerxkj22dmoxoq553qd.onion/',
                'description' => 'nitter frontend - alternative to twitter PRIVACY FOCUSED.',
                'category' => Category::SOCIAL_NETWORKS,
            ],

            // FORUMS_COMMUNITIES
            [
                'title' => 'Raddle',
                'url' => 'http://c32zjeghcp5tj3kb72pltz56piei66drc63vkhn5yixiyk4cmerrjtid.onion/',
                'description' => 'Reddit type large forum.',
                'category' => Category::FORUMS_COMMUNITIES,
            ],
            [
                'title' => 'Endchan',
                'url' => 'http://enxx3byspwsdo446jujc52ucy2pf5urdbhqw3kbsfhlfjwmbpj5smdad.onion/',
                'description' => 'The imageboard at the end of the universe.',
                'category' => Category::FORUMS_COMMUNITIES,
            ],

            // BLOGS_NEWS
            [
                'title' => 'Darknet Live',
                'url' => 'http://darkzzx4avcsuofgfez5zq75cqc4mprjvfqywo45dfcaxrwqg6qrlfid.onion/',
                'description' => 'Popular news site about Darknet matters.',
                'category' => Category::BLOGS_NEWS,
            ],
            [
                'title' => 'The Intercept',
                'url' => 'http://27m3p2uv7igmj6kvd4ql3cct5h3sdwrsajovkkndeufumzyfhlfev4qd.onion/',
                'description' => 'Fearless, adversarial journalism that holds the powerful accountable.',
                'category' => Category::BLOGS_NEWS,
            ],

            // WHISTLEBLOWING
            [
                'title' => 'SecureDrop',
                'url' => 'http://sdolvtfhatvsysc6l34d65ymdwxcujausv7k5jk4cy5ttzhjoi6fzvyd.onion/',
                'description' => 'The open-source whistleblower submission system managed by Freedom of the Press Foundation.',
                'category' => Category::WHISTLEBLOWING,
            ],

            // FILE_SHARING
            [
                'title' => 'ZeroBin',
                'url' => 'http://zerobinftagjpeeebbvyzjcqyjpmjvynj5qlexwyxe7l3vqejxnqv5qd.onion/',
                'description' => 'ZeroBin is a minimalist, opensource online pastebin where the server has zero knowledge of pasted data.',
                'category' => Category::FILE_SHARING,
            ],
            [
                'title' => 'OnionShare',
                'url' => 'http://lldan5gahapx5k7iafb3s4ikijc4ni7gx5iywdflkba5y2ezyg6sjgyd.onion/',
                'description' => 'Securely and anonymously share files, host websites, and chat with friends using the Tor network.',
                'category' => Category::FILE_SHARING,
            ],

            // HOSTING_SERVICES
            [
                'title' => 'Njalla',
                'url' => 'http://njallalafimoej5i4eg7vlnqjvmb6zhdh27qxcatdn647jtwwwui3nad.onion/',
                'description' => 'Privacy-focused domain and VPS provider.',
                'category' => Category::HOSTING_SERVICES,
            ],
            [
                'title' => 'SporeStack',
                'url' => 'http://spore64i5sofqlfz5gq2ju4msgzojjwifls7rok2cti624zyq3fcelad.onion/',
                'description' => 'API-driven VPS hosting for Bitcoin.',
                'category' => Category::HOSTING_SERVICES,
            ],

            // SOFTWARE_DEVELOPMENT
            [
                'title' => 'Darktea',
                'url' => 'http://it7otdanqu7ktntxzm427cba6i53w6wlanlh23v5i3siqmos47pzhvyd.onion/',
                'description' => 'Host your own git repositories on the Tor deep web.',
                'category' => Category::SOFTWARE_DEVELOPMENT,
            ],
            [
                'title' => 'Forgejo',
                'url' => 'http://qt5vr747phiq55ubqip4hflmpygzl374mum2zbyqdxg6sqbngmzlqhid.onion/',
                'description' => 'A painless, self-hosted Git service.',
                'category' => Category::SOFTWARE_DEVELOPMENT,
            ],

            // CRYPTO_BLOCKCHAIN
            [
                'title' => 'Wasabi Wallet',
                'url' => 'http://wasabiukrxmkdgve5kynjztuovbg43uxcbcxn6y2okcrsg7gb6jdmbad.onion/',
                'description' => 'Keep your funds untraceable with the reliable high volume bitcoin mixer.',
                'category' => Category::CRYPTO_BLOCKCHAIN,
            ],
            [
                'title' => 'BisQ',
                'url' => 'http://s3p666he6q6djb6u3ekjdkmoyd77w63zq6gqf6sde54yg6bdfqukz2qd.onion/Main_Page',
                'description' => 'Decentralized bitcoin exchange network.',
                'category' => Category::CRYPTO_BLOCKCHAIN,
            ],

            // BOOKS_ARCHIVES
            [
                'title' => 'Imperial Library of Trantor',
                'url' => 'http://kx5thpx2olielkihfyo4jgjqfb7zx7wxr3sd4xzt26ochei4m6f7tayd.onion/',
                'description' => 'Close to one million books available.',
                'category' => Category::BOOKS_ARCHIVES,
            ],
            [
                'title' => 'The Anarchist Library',
                'url' => 'http://libraryqxxiqakubqv3dc2bend2koqsndbwox2johfywcatxie26bsad.onion/',
                'description' => 'Thousands of essays, books and articles.',
                'category' => Category::BOOKS_ARCHIVES,
            ],

            // GENERAL_KNOWLEDGE
            [
                'title' => 'Psychonaut Wiki',
                'url' => 'http://vvedndyt433kopnhv6vejxnut54y5752vpxshjaqmj7ftwiu6quiv2ad.onion/wiki/Main_Page',
                'description' => 'Encyclopedia that aims to document the field of psychonautics.',
                'category' => Category::GENERAL_KNOWLEDGE,
            ],

            // EROTIC_PORN
            [
                'title' => 'TeenPorn (Amateur)',
                'url' => 'http://tgtwiba5y3i3vh4n3a55utgpabwu5wx26eqqbgwrxd6gmc73rbxdclad.onion/',
                'description' => 'Amateur teen porn selection.',
                'category' => Category::EROTIC_PORN,
            ],

            // OTHER
            [
                'title' => 'Guns Dark Market',
                'url' => 'http://bmgunsyop5qa34nzrayd6shsovsukwbbscyo2hbu3ri7b2ghw6sjgrad.onion/',
                'description' => 'Guns market for various firearms.',
                'category' => Category::OTHER,
            ],
            [
                'title' => 'Hidden Wiki Official 2026',
                'url' => 'http://zqktlwiuavvvqqt4ybvgvi7tyo4hjl5xgfuvpdf6otjiycgwqbym2qad.onion/',
                'description' => 'Official 2026 Hidden Wiki mirror.',
                'category' => Category::OTHER,
            ],
        ];

        foreach ($links as $linkData) {
            Link::create([
                'title' => $linkData['title'],
                'url' => $linkData['url'],
                'description' => $linkData['description'],
                'category' => $linkData['category'],
                'slug' => Str::slug($linkData['title']) . '-' . Str::random(5),
                'status' => Status::ACTIVE,
                'uptime_status' => UptimeStatus::ONLINE,
                'user_id' => 1,
                'last_check' => now(),
            ]);
        }
    }
}
