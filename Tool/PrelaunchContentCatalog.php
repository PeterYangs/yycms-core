<?php

namespace Ycore\Tool;

class PrelaunchContentCatalog
{
    private const IMAGE_COUNT = 24;

    private array $defaultGameCategoryIds = [2, 6, 7, 8, 9, 10, 11, 12, 13, 28, 33];

    private array $defaultAppCategoryIds = [4, 14, 15, 16, 17, 18, 19, 29, 31, 34];

    private array $categoryImageKeys = [
        2 => 'game-rpg',
        6 => 'game-action',
        7 => 'game-music',
        8 => 'game-racing',
        9 => 'game-casual',
        10 => 'game-sim',
        11 => 'game-sport',
        12 => 'game-card',
        13 => 'game-shooter',
        28 => 'game-adventure',
        33 => 'game-love',
        4 => 'app-travel',
        14 => 'app-finance',
        15 => 'app-chat',
        16 => 'app-work',
        17 => 'app-read',
        18 => 'app-photo',
        19 => 'app-tool',
        29 => 'app-video',
        31 => 'app-shopping',
        34 => 'app-life',
    ];

    public function defaultGameCategoryIds(): array
    {
        return $this->defaultGameCategoryIds;
    }

    public function defaultAppCategoryIds(): array
    {
        return $this->defaultAppCategoryIds;
    }

    public function rankImagePool(): array
    {
        return $this->imagesForKey('mobile-game-rank');
    }

    public function imagePoolForCategory(int $categoryId): array
    {
        if (!isset($this->categoryImageKeys[$categoryId])) {
            return [];
        }

        return $this->imagesForKey($this->categoryImageKeys[$categoryId]);
    }

    public function rankDefinition(int $slot, string $seed): array
    {
        $definitions = $this->seededOrder($this->rankDefinitions(), $seed, 'rank-definitions');

        return $definitions[($slot - 1) % count($definitions)];
    }

    public function collectTitle(string $type, string $categoryName): string
    {
        if ($type === 'app_collect') {
            return $this->removeDuplicateSuffix($categoryName, ['合集']) . '合集';
        }

        $name = $this->removeDuplicateSuffix($categoryName, ['游戏', '手游', '合集']);

        return $name . '手游合集';
    }

    public function seoFor(string $type, string $title, string $subject, string $siteName, string $seed, int $slot): array
    {
        $titleTemplate = $this->pick($this->seoTitleTemplates($type), $seed, "{$type}:seo-title:{$slot}");
        $descTemplate = $this->pick($this->seoDescriptionTemplates($type), $seed, "{$type}:seo-desc:{$slot}");
        $modifier = $this->pick($this->seoModifiers($type), $seed, "{$type}:modifier:{$slot}");
        $keywords = $this->keywordList($type, $title, $subject, $seed, $slot);

        $replace = [
            '[title]' => $title,
            '[subject]' => $subject,
            '[site]' => $siteName ?: '本站',
            '[modifier]' => $modifier,
        ];

        return [
            'seo_title' => $this->limitText(strtr($titleTemplate, $replace), 180),
            'seo_keyword' => $this->limitText(implode(',', $keywords), 220),
            'seo_desc' => $this->limitText(strtr($descTemplate, $replace), 240),
        ];
    }

    public function contentFor(
        string $type,
        string $title,
        string $subject,
        array $articles,
        string $marker,
        string $siteName,
        string $seed,
        int $slot
    ): string {
        $template = $this->pick($this->paragraphTemplates($type), $seed, "{$type}:paragraph:{$slot}");
        $siteName = $siteName ?: '本站';
        $articleTitles = array_values(array_filter(array_map(
            fn (array $article): string => trim((string)($article['title'] ?? '')),
            array_slice($articles, 0, 4)
        )));

        $replace = [
            '[title]' => $this->escape($title),
            '[subject]' => $this->escape($subject),
            '[site]' => $this->escape($siteName),
            '[count]' => (string)count($articles),
            '[examples]' => $this->escape(implode('、', $articleTitles)),
        ];

        return $marker . '<p>' . $this->normalizeParagraph(strtr($template, $replace)) . '</p>';
    }

    public function seoCombinationCount(string $type): int
    {
        return count($this->seoTitleTemplates($type))
            * count($this->seoDescriptionTemplates($type))
            * count($this->keywordSeeds($type))
            * count($this->seoModifiers($type));
    }

    public function imageFor(string $type, ?int $categoryId, string $seed, int $slot): string
    {
        $pool = $type === 'rank' ? $this->rankImagePool() : $this->imagePoolForCategory((int)$categoryId);

        if (!$pool) {
            return '';
        }

        $pool = $this->seededOrder($pool, $seed, "{$type}:image:{$categoryId}");

        return $pool[($slot - 1) % count($pool)];
    }

    private function imagesForKey(string $key): array
    {
        $themes = str_starts_with($key, 'app-')
            ? $this->businessImageThemes()
            : $this->gameImageThemes();

        return array_map(
            fn (array $theme): string => sprintf(
                'https://placehold.co/640x360/%s/%s.jpg?text=%s',
                $theme['bg'],
                $theme['fg'],
                rawurlencode($theme['text'])
            ),
            array_slice($themes, 0, self::IMAGE_COUNT)
        );
    }

    private function gameImageThemes(): array
    {
        return [
            ['bg' => '111827', 'fg' => 'ffffff', 'text' => 'Mobile Game'],
            ['bg' => '1f2937', 'fg' => 'f9fafb', 'text' => 'Gaming Picks'],
            ['bg' => '0f172a', 'fg' => 'e0f2fe', 'text' => 'Game Ranking'],
            ['bg' => '312e81', 'fg' => 'ffffff', 'text' => 'Game Controller'],
            ['bg' => '4c1d95', 'fg' => 'f5f3ff', 'text' => 'Console Gaming'],
            ['bg' => '7c2d12', 'fg' => 'fff7ed', 'text' => 'Esports Games'],
            ['bg' => '164e63', 'fg' => 'ecfeff', 'text' => 'Arcade Game'],
            ['bg' => '166534', 'fg' => 'f0fdf4', 'text' => 'Mobile Gaming'],
            ['bg' => '991b1b', 'fg' => 'fef2f2', 'text' => 'Action Game'],
            ['bg' => '1e3a8a', 'fg' => 'eff6ff', 'text' => 'Racing Game'],
            ['bg' => '713f12', 'fg' => 'fefce8', 'text' => 'Adventure Game'],
            ['bg' => '581c87', 'fg' => 'faf5ff', 'text' => 'Role Playing Game'],
            ['bg' => '0f766e', 'fg' => 'f0fdfa', 'text' => 'Strategy Game'],
            ['bg' => '7f1d1d', 'fg' => 'fef2f2', 'text' => 'Shooting Game'],
            ['bg' => '155e75', 'fg' => 'ecfeff', 'text' => 'Card Game'],
            ['bg' => '365314', 'fg' => 'f7fee7', 'text' => 'Puzzle Game'],
            ['bg' => '854d0e', 'fg' => 'fefce8', 'text' => 'Sports Game'],
            ['bg' => 'be123c', 'fg' => 'fff1f2', 'text' => 'Simulation Game'],
            ['bg' => '1d4ed8', 'fg' => 'eff6ff', 'text' => 'Casual Game'],
            ['bg' => '047857', 'fg' => 'ecfdf5', 'text' => 'Online Game'],
            ['bg' => '4338ca', 'fg' => 'eef2ff', 'text' => 'Controller Gaming'],
            ['bg' => '0e7490', 'fg' => 'ecfeff', 'text' => 'Game Console'],
            ['bg' => 'b45309', 'fg' => 'fffbeb', 'text' => 'Multiplayer Game'],
            ['bg' => '9d174d', 'fg' => 'fdf2f8', 'text' => 'Gaming Collection'],
        ];
    }

    private function businessImageThemes(): array
    {
        return [
            ['bg' => '1f2937', 'fg' => 'ffffff', 'text' => 'Business Apps'],
            ['bg' => '374151', 'fg' => 'f9fafb', 'text' => 'Office Tools'],
            ['bg' => '0f172a', 'fg' => 'e0f2fe', 'text' => 'Office Work'],
            ['bg' => '1e3a8a', 'fg' => 'eff6ff', 'text' => 'Business Meeting'],
            ['bg' => '164e63', 'fg' => 'ecfeff', 'text' => 'Laptop Work'],
            ['bg' => '365314', 'fg' => 'f7fee7', 'text' => 'Workspace Apps'],
            ['bg' => '713f12', 'fg' => 'fefce8', 'text' => 'Team Work'],
            ['bg' => '4c1d95', 'fg' => 'f5f3ff', 'text' => 'Conference Tools'],
            ['bg' => '7c2d12', 'fg' => 'fff7ed', 'text' => 'Office Desk'],
            ['bg' => '0f766e', 'fg' => 'f0fdfa', 'text' => 'Coworking Apps'],
            ['bg' => '155e75', 'fg' => 'ecfeff', 'text' => 'Business Laptop'],
            ['bg' => '581c87', 'fg' => 'faf5ff', 'text' => 'Work Meeting'],
            ['bg' => '166534', 'fg' => 'f0fdf4', 'text' => 'Office Team'],
            ['bg' => '312e81', 'fg' => 'ffffff', 'text' => 'Business Office'],
            ['bg' => '854d0e', 'fg' => 'fefce8', 'text' => 'Laptop Office'],
            ['bg' => '991b1b', 'fg' => 'fef2f2', 'text' => 'Desk Work'],
            ['bg' => '0e7490', 'fg' => 'ecfeff', 'text' => 'Conference Room'],
            ['bg' => '047857', 'fg' => 'ecfdf5', 'text' => 'Team Office'],
            ['bg' => '1d4ed8', 'fg' => 'eff6ff', 'text' => 'Workplace Apps'],
            ['bg' => 'b45309', 'fg' => 'fffbeb', 'text' => 'Business Plan'],
            ['bg' => '4338ca', 'fg' => 'eef2ff', 'text' => 'Office Meeting'],
            ['bg' => 'be123c', 'fg' => 'fff1f2', 'text' => 'Business Team'],
            ['bg' => '9d174d', 'fg' => 'fdf2f8', 'text' => 'Laptop Desk'],
            ['bg' => '7f1d1d', 'fg' => 'fef2f2', 'text' => 'Work Apps'],
        ];
    }

    private function rankDefinitions(): array
    {
        return [
            ['title' => '热门手游排行榜', 'subject' => '热门手游'],
            ['title' => '新游期待榜', 'subject' => '新游'],
            ['title' => '耐玩手游排行榜', 'subject' => '耐玩手游'],
            ['title' => '高人气手游榜', 'subject' => '高人气手游'],
            ['title' => '安卓手游下载榜', 'subject' => '安卓手游'],
            ['title' => '精品手游推荐榜', 'subject' => '精品手游'],
            ['title' => '多人联机手游榜', 'subject' => '多人联机手游'],
            ['title' => '单机手游排行榜', 'subject' => '单机手游'],
            ['title' => '休闲手游排行榜', 'subject' => '休闲手游'],
            ['title' => '角色扮演手游榜', 'subject' => '角色扮演手游'],
            ['title' => '动作手游排行榜', 'subject' => '动作手游'],
            ['title' => '策略手游排行榜', 'subject' => '策略手游'],
        ];
    }

    private function seoTitleTemplates(string $type): array
    {
        return match ($type) {
            'app_collect' => [
                '[title]推荐-[subject]哪个好用-[site][modifier]',
                '[subject]软件合集-[title]下载推荐-[site]',
                '[title]有哪些-好用的[subject]app大全',
                '[subject]app排行榜-[title]精选下载',
                '[title]软件大全-[modifier]应用推荐',
                '[subject]应用合集-[title]免费下载',
                '好用的[title]软件推荐-[site][modifier]',
                '[subject]软件下载合集-[title]安装推荐',
                '[title]app合集-[subject]软件哪个好',
                '[modifier][subject]应用推荐-[title]',
            ],
            'game_collect' => [
                '[title]推荐-[subject]哪个好玩-[site][modifier]',
                '[subject]游戏合集-[title]下载大全',
                '好玩的[title]-[subject]手游推荐',
                '[title]手游排行榜-[subject]游戏下载',
                '[subject]手游大全-[title]免费下载',
                '[title]有哪些-[modifier]手游合集',
                '[subject]游戏推荐-[title]精选',
                '[title]下载-[subject]安卓手游大全',
                '[modifier][subject]手游推荐-[title]',
                '[title]专题-[subject]热门手游合集',
            ],
            default => [
                '[title]-[subject]下载排行榜-[site]',
                '[title]前十名-[subject]推荐下载',
                '[modifier][title]-好玩的[subject]大全',
                '[title]最新榜单-[subject]安卓下载',
                '[subject]手游排行榜-[title]精选',
                '[title]推荐-[subject]哪个好玩',
                '[subject]下载榜-[title][modifier]',
                '[title]大全-热门[subject]排行',
                '[title]2026-[subject]游戏下载推荐',
                '[modifier][subject]排行榜-[title]',
            ],
        };
    }

    private function seoDescriptionTemplates(string $type): array
    {
        return match ($type) {
            'app_collect' => [
                '[site]整理[title]，覆盖常用[subject]工具，方便用户快速找到稳定、好用、更新及时的手机应用。',
                '想找[subject]软件可以查看[title]，这里按体验、热度和实用性筛选出适合日常安装的应用。',
                '[title]收录多款[modifier]应用，适合需要[subject]服务的用户下载、对比和长期使用。',
                '这里提供[title]相关应用推荐，帮助你从功能、口碑和使用场景中挑选更合适的软件。',
                '[site]为你准备[subject]应用合集，包含多款热门软件，满足办公、生活和娱乐等不同需求。',
                '不知道[subject]app哪个好用，可以参考[title]，快速了解当前值得安装的应用选择。',
                '[title]持续整理实用软件资源，帮助用户节省查找时间，轻松获得可靠的[subject]应用。',
                '本页聚合[modifier]的[subject]软件，适合新机装机、日常换机和同类应用对比。',
                '[title]将常见[subject]应用集中展示，方便用户按需求选择下载并体验。',
                '[site]推荐的[title]兼顾热度和实用性，为用户提供更清晰的手机软件选择参考。',
            ],
            'game_collect' => [
                '[site]整理[title]，收录多款[subject]，方便玩家快速找到值得下载体验的手机游戏。',
                '想玩[subject]可以查看[title]，这里按玩法、热度和口碑筛选出适合长期体验的手游。',
                '[title]包含多款[modifier]手游，适合喜欢[subject]玩法的玩家下载、试玩和收藏。',
                '这里提供[title]相关游戏推荐，帮助玩家从题材、玩法和人气中挑选更合适的手游。',
                '[site]为你准备[subject]手游合集，覆盖热门、新作和经典作品，满足不同玩家偏好。',
                '不知道[subject]哪个好玩，可以参考[title]，快速了解当前值得安装的手机游戏。',
                '[title]持续整理优质手游资源，帮助玩家节省查找时间，轻松获得好玩的[subject]。',
                '本页聚合[modifier]的[subject]，适合新玩家入坑、老玩家换游和同类游戏对比。',
                '[title]将常见[subject]集中展示，方便玩家按兴趣选择下载并体验。',
                '[site]推荐的[title]兼顾人气和耐玩度，为玩家提供更清晰的手游选择参考。',
            ],
            default => [
                '[site]整理[title]，精选多款[subject]，帮助玩家快速了解当前值得下载的热门手游。',
                '本榜单围绕[subject]热度、下载需求和玩法口碑进行推荐，适合想找新游戏的玩家参考。',
                '[title]收录近期[modifier]手游，覆盖多种玩法类型，方便玩家按兴趣挑选体验。',
                '想知道[subject]哪些值得玩，可以查看[title]，快速发现当前人气较高的手机游戏。',
                '[site]为玩家准备[title]，聚合热门作品和精品游戏，让下载选择更省心。',
                '这里持续整理[subject]榜单内容，帮助玩家在众多手游中找到更适合自己的选择。',
                '[title]兼顾热度、玩法和更新活跃度，为喜欢[subject]的玩家提供参考。',
                '本页推荐多款[modifier]手机游戏，适合想尝试新作、热门作和耐玩作品的玩家。',
                '[title]把近期值得关注的[subject]集中展示，方便玩家快速下载体验。',
                '[site]提供[subject]排行推荐，让你不用反复搜索也能找到好玩的手游。',
            ],
        };
    }

    private function seoModifiers(string $type): array
    {
        return match ($type) {
            'app_collect' => ['热门', '免费', '实用', '常用', '新版', '精选', '高人气', '装机必备', '安卓', '好评'],
            'game_collect' => ['热门', '免费', '耐玩', '经典', '新版', '精选', '高人气', '安卓', '好评', '精品'],
            default => ['热门', '免费', '耐玩', '高分', '新版', '精选', '高人气', '安卓', '好评', '精品'],
        };
    }

    private function keywordSeeds(string $type): array
    {
        return match ($type) {
            'app_collect' => ['app下载', '手机软件', '安卓应用', '软件合集', '应用推荐', '免费软件', '实用app', '装机软件', '热门应用', '常用软件', '新版软件', '应用大全'],
            'game_collect' => ['手游下载', '手机游戏', '安卓游戏', '游戏合集', '手游推荐', '免费手游', '热门手游', '耐玩手游', '游戏大全', '精品手游', '新游推荐', '好玩手游'],
            default => ['手游排行榜', '手机游戏下载', '热门手游', '安卓手游', '新游期待榜', '手游推荐', '耐玩手游', '高分手游', '游戏下载榜', '精品手游', '多人手游', '单机手游'],
        };
    }

    private function keywordList(string $type, string $title, string $subject, string $seed, int $slot): array
    {
        $keywords = array_merge([$title, $subject], $this->keywordSeeds($type));
        $keywords = array_values(array_unique(array_filter($keywords)));

        return array_slice($this->seededOrder($keywords, $seed, "{$type}:keywords:{$slot}"), 0, 8);
    }

    private function paragraphTemplates(string $type): array
    {
        return match ($type) {
            'app_collect' => [
                '想让手机里的[subject]更顺手吗？不妨看看这份[title]！这里整理了[count]款常用应用，像[examples]等都很适合日常安装使用，覆盖效率、生活与实用工具等多种需求，让你少花时间筛选，多一点轻松体验。',
                '正在找好用的[subject]软件？[title]把近期值得关注的[count]款应用放在一起，包含[examples]等选择。它们各有侧重，有的操作简单，有的功能全面，适合换机装机或寻找同类替代时参考。',
                '如果你想快速找到稳定又实用的[subject]应用，这份[title]值得收藏。我们从常用场景出发整理了[count]款软件，包含[examples]等热门选择，方便你按功能、习惯和使用频率挑选。',
                '[title]适合正在寻找[subject]工具的用户查看，里面收录了[count]款应用，包含[examples]等内容。无论是日常使用、效率提升还是同类软件对比，都能帮你更快找到合适的下载选择。',
                '想给手机补齐几款靠谱的[subject]应用？可以从[title]开始看起。这里精选了[count]款软件，像[examples]等都比较适合长期使用，兼顾上手难度、功能覆盖和日常使用频率。',
            ],
            'game_collect' => [
                '想在忙碌的生活中找到一丝轻松与乐趣吗？不妨试试这份[title]！这里精选了[count]款[subject]，包含[examples]等热门选择。它们或考验操作，或主打剧情，更有视觉与玩法的双重惊喜，让你在闲暇之余也能沉浸体验。',
                '喜欢[subject]却不知道先玩哪款？这份[title]把[count]款值得体验的手游整理在一起，像[examples]等都各有亮点。无论你偏爱轻松上手还是长期养成，都能从这里找到合适的下载选择。',
                '[title]为喜欢[subject]的玩家准备了[count]款手游，包含[examples]等内容。每一款都围绕同类玩法展开，有的节奏轻快，有的内容扎实，适合想换游戏、找新作或收藏同类作品时参考。',
                '想找几款好玩的[subject]放松一下？可以看看这份[title]。这里收录了[count]款手游，包含[examples]等推荐，既有适合碎片时间体验的作品，也有适合慢慢深入的耐玩选择。',
                '如果你正在寻找[subject]，这份[title]会更省心。我们整理了[count]款相关手游，像[examples]等都比较适合下载试玩，题材、节奏和玩法各不相同，方便你按兴趣挑选。',
            ],
            default => [
                '想知道近期哪些[subject]更值得下载？这份[title]整理了[count]款热门手游，包含[examples]等选择。它们有的轻松耐玩，有的节奏紧张，也有适合长期体验的精品作品，方便玩家快速找到合适的游戏。',
                '[title]为玩家准备了[count]款[subject]，像[examples]等都值得关注。榜单兼顾热度、玩法和下载需求，适合想找新作、换游戏或补充装机游戏的玩家参考。',
                '如果你正在纠结[subject]哪个好玩，可以先看看这份[title]。这里精选了[count]款手游，包含[examples]等内容，覆盖不同题材和玩法节奏，让选择更直观。',
                '想在手机里多备几款好玩的[subject]？[title]收录了[count]款近期关注度较高的作品，包含[examples]等推荐。无论是休闲放松还是深度体验，都能找到适合自己的方向。',
                '[site]整理的[title]聚合了[count]款[subject]，包含[examples]等热门内容。榜单会从玩家关注度、玩法特色和体验门槛出发，帮助你更快挑到想玩的手游。',
            ],
        };
    }

    private function removeDuplicateSuffix(string $value, array $suffixes): string
    {
        foreach ($suffixes as $suffix) {
            if (str_ends_with($value, $suffix)) {
                return mb_substr($value, 0, mb_strlen($value) - mb_strlen($suffix));
            }
        }

        return $value;
    }

    private function seededOrder(array $items, string $seed, string $salt): array
    {
        $decorated = [];

        foreach (array_values($items) as $index => $item) {
            $decorated[] = [
                'key' => hash('sha256', $seed . '|' . $salt . '|' . $index . '|' . serialize($item)),
                'item' => $item,
            ];
        }

        usort($decorated, fn (array $a, array $b): int => strcmp($a['key'], $b['key']));

        return array_column($decorated, 'item');
    }

    private function pick(array $items, string $seed, string $salt): mixed
    {
        if (!$items) {
            return '';
        }

        $hash = hash('sha256', $seed . '|' . $salt);
        $index = hexdec(substr($hash, 0, 8)) % count($items);

        return $items[$index];
    }

    private function limitText(string $value, int $length): string
    {
        return mb_substr(trim($value), 0, $length);
    }

    private function normalizeParagraph(string $value): string
    {
        return trim(preg_replace('/\s+/u', '', $value) ?? $value);
    }

    private function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
