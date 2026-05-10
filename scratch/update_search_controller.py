import sys

path = '/home/idrift/Project/github/listlink/app/Http/Controllers/SearchController.php'
with open(path, 'r') as f:
    lines = f.readlines()

# Line 75 is already modified by sed (index 74)
# We want to replace lines 76 to 86 (index 75 to 85)

new_body = """                        $isExact = $interpretation['is_exact'] ?? false;
                        if ($isExact) {
                            $cleanExact = trim($effectiveQuery, '"');
                            $q->where('links.title', 'LIKE', "%{$cleanExact}%")
                              ->orWhere('links.description', 'LIKE', "%{$cleanExact}%")
                              ->orWhere('links.url', 'LIKE', "%{$cleanExact}%");
                        } else {
                            $q->whereRaw('MATCH(links.title, links.description) AGAINST(? IN BOOLEAN MODE)', [$effectiveQuery])
                              ->orWhereRaw('MATCH(crawl_contents.h1, crawl_contents.meta_description, crawl_contents.body_text) AGAINST(? IN BOOLEAN MODE)', [$effectiveQuery])
                              ->orWhere('links.url', 'LIKE', "%{$effectiveQuery}%");

                            foreach ($searchTokens as $token) {
                                if (mb_strlen($token) >= 3) {
                                    $q->orWhere('links.title', 'LIKE', "%{$token}%")
                                      ->orWhere('links.description', 'LIKE', "%{$token}%");
                                }
                            }

                            foreach (array_slice($synonyms, 0, 3) as $syn) {
                                if (mb_strlen($syn) >= 3) {
                                    $q->orWhere('links.title', 'LIKE', "%{$syn}%")
                                      ->orWhere('links.description', 'LIKE', "%{$syn}%");
                                }
                            }
                        }
"""

# Replace lines 76 to 86
# Note: lines in file are 1-indexed, so line 76 is index 75.
# We replace lines[75:86] (which is line 76 to 86 inclusive)
lines[75:86] = [new_body]

with open(path, 'w') as f:
    f.writelines(lines)

print("Successfully updated SearchController.php")
