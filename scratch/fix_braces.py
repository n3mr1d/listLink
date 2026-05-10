import sys

path = '/home/idrift/Project/github/listlink/app/Services/SearchService.php'
with open(path, 'r') as f:
    lines = f.readlines()

# Let's find the problematic area and fix it.
# We want to remove the extra brace after the phonetic matching loop.

start_index = -1
for i, line in enumerate(lines):
    if 'foreach ($tokens as $token) {' in line and 'Phonetic (Fuzzy) Matching' in lines[i-1]:
        start_index = i
        break

if start_index != -1:
    # Look for the end of this foreach loop
    # It should have 5 closing braces: 
    # 1. if ($docWord !== $token)
    # 2. if (mb_strlen($docWord) >= 4 && metaphone($docWord) === $tMeta)
    # 3. foreach ($docVector as $docWord)
    # 4. if (mb_strlen($token) >= 4)
    # 5. foreach ($tokens as $token)
    
    # Let's just manually fix the area from line 260 to 270 (approx)
    # Based on the last snapshot:
    # 263:                             }
    # 264:                         }
    # 265:                     }
    # 266:                 }
    # 267:             }
    # 268:             } <-- EXTRA
    
    # I'll just look for double braces and remove one if it's followed by a comment.
    for i in range(len(lines) - 1):
        if lines[i].strip() == '}' and lines[i+1].strip() == '}' and '// ── 3. Phrase Proximity' in lines[i+2]:
             del lines[i+1]
             break

    with open(path, 'w') as f:
        f.writelines(lines)
    print("Fixed SearchService.php")
else:
    print("Could not find start index")
