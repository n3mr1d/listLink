import sys

path = '/home/idrift/Project/github/listlink/app/Services/SearchService.php'
with open(path, 'r') as f:
    lines = f.readlines()

target_start = -1
for i, line in enumerate(lines):
    if '// ── 2. Synonym Matching (lower weight) ──────────────────' in line:
        target_start = i
        break

if target_start != -1:
    new_block = """            // ── 2. Synonym & Phonetic Matching ──────────────────────
            foreach ($synonyms as $syn) {
                $lower = mb_strtolower($syn);
                if (str_contains($title, $lower)) {
                    $score += 5;
                    $reasons[] = "Synonym '{$syn}' in title";
                } elseif (str_contains($allText, $lower)) {
                    $score += 2;
                }
            }

            // Phonetic (Fuzzy) Matching
            foreach ($tokens as $token) {
                if (mb_strlen($token) >= 4) {
                    $tMeta = metaphone($token);
                    foreach ($docVector as $docWord) {
                        if (mb_strlen($docWord) >= 4 && metaphone($docWord) === $tMeta) {
                            if ($docWord !== $token) {
                                $score += 3;
                                $reasons[] = "Fuzzy match: {$docWord}";
                            }
                        }
                    }
                }
            }
"""
    # Replace the next 8 lines (the original synonym matching block)
    lines[target_start:target_start+9] = [new_block]

    with open(path, 'w') as f:
        f.writelines(lines)
    print("Successfully updated SearchService.php")
else:
    print("Could not find target block")
