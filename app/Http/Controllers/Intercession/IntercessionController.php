<?php

namespace App\Http\Controllers\Intercession;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\ManagesActionPlans;
use App\Models\Intercession\SpiritualForm as Form;
use App\Models\Intercession\FormSubmission;
use App\Models\Intercession\ActionPlan;
use App\Models\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log; 
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;

class IntercessionController extends Controller
{
    use ManagesActionPlans;

    protected ?string $actionPlanDepartment = 'intercession';

    protected array $bibleStudyVersions = [
        'bysb' => ['id' => 351, 'code' => 'BYSB', 'label' => 'Bibiliya YERA'],
        'bir' => ['id' => 395, 'code' => 'BIR', 'label' => "Bibiliya Ijambo ry'Imana"],
        'niv' => ['id' => 111, 'code' => 'NIV', 'label' => 'NIV'],
        'kjv' => ['id' => 1, 'code' => 'KJV', 'label' => 'KJV'],
        'esv' => ['id' => 59, 'code' => 'ESV', 'label' => 'ESV'],
    ];

    protected string $bibleStudyDefaultCompare = '';

    protected string $bibleLocalDirectory = 'D:\\MY.DATA\\Reverence\\reverence-worship-main\\reverence-worship-main\\Bibles';

    protected array $bibleLocalVersionFiles = [
        'bysb' => 'BY.xml',
        'bir' => 'BIR.xml',
        'niv' => 'NIV.xml',
        'kjv' => 'KJV.xml',
        'esv' => 'ESV.xml',
    ];

    protected array $bibleStudyBooks = [
        ['code' => 'GEN', 'name' => 'Genesis'],
        ['code' => 'EXO', 'name' => 'Exodus'],
        ['code' => 'LEV', 'name' => 'Leviticus'],
        ['code' => 'NUM', 'name' => 'Numbers'],
        ['code' => 'DEU', 'name' => 'Deuteronomy'],
        ['code' => 'JOS', 'name' => 'Joshua'],
        ['code' => 'JDG', 'name' => 'Judges'],
        ['code' => 'RUT', 'name' => 'Ruth'],
        ['code' => '1SA', 'name' => '1 Samuel'],
        ['code' => '2SA', 'name' => '2 Samuel'],
        ['code' => '1KI', 'name' => '1 Kings'],
        ['code' => '2KI', 'name' => '2 Kings'],
        ['code' => '1CH', 'name' => '1 Chronicles'],
        ['code' => '2CH', 'name' => '2 Chronicles'],
        ['code' => 'EZR', 'name' => 'Ezra'],
        ['code' => 'NEH', 'name' => 'Nehemiah'],
        ['code' => 'EST', 'name' => 'Esther'],
        ['code' => 'JOB', 'name' => 'Job'],
        ['code' => 'PSA', 'name' => 'Psalms'],
        ['code' => 'PRO', 'name' => 'Proverbs'],
        ['code' => 'ECC', 'name' => 'Ecclesiastes'],
        ['code' => 'SNG', 'name' => 'Song of Solomon'],
        ['code' => 'ISA', 'name' => 'Isaiah'],
        ['code' => 'JER', 'name' => 'Jeremiah'],
        ['code' => 'LAM', 'name' => 'Lamentations'],
        ['code' => 'EZK', 'name' => 'Ezekiel'],
        ['code' => 'DAN', 'name' => 'Daniel'],
        ['code' => 'HOS', 'name' => 'Hosea'],
        ['code' => 'JOL', 'name' => 'Joel'],
        ['code' => 'AMO', 'name' => 'Amos'],
        ['code' => 'OBA', 'name' => 'Obadiah'],
        ['code' => 'JON', 'name' => 'Jonah'],
        ['code' => 'MIC', 'name' => 'Micah'],
        ['code' => 'NAM', 'name' => 'Nahum'],
        ['code' => 'HAB', 'name' => 'Habakkuk'],
        ['code' => 'ZEP', 'name' => 'Zephaniah'],
        ['code' => 'HAG', 'name' => 'Haggai'],
        ['code' => 'ZEC', 'name' => 'Zechariah'],
        ['code' => 'MAL', 'name' => 'Malachi'],
        ['code' => 'MAT', 'name' => 'Matthew'],
        ['code' => 'MRK', 'name' => 'Mark'],
        ['code' => 'LUK', 'name' => 'Luke'],
        ['code' => 'JHN', 'name' => 'John'],
        ['code' => 'ACT', 'name' => 'Acts'],
        ['code' => 'ROM', 'name' => 'Romans'],
        ['code' => '1CO', 'name' => '1 Corinthians'],
        ['code' => '2CO', 'name' => '2 Corinthians'],
        ['code' => 'GAL', 'name' => 'Galatians'],
        ['code' => 'EPH', 'name' => 'Ephesians'],
        ['code' => 'PHP', 'name' => 'Philippians'],
        ['code' => 'COL', 'name' => 'Colossians'],
        ['code' => '1TH', 'name' => '1 Thessalonians'],
        ['code' => '2TH', 'name' => '2 Thessalonians'],
        ['code' => '1TI', 'name' => '1 Timothy'],
        ['code' => '2TI', 'name' => '2 Timothy'],
        ['code' => 'TIT', 'name' => 'Titus'],
        ['code' => 'PHM', 'name' => 'Philemon'],
        ['code' => 'HEB', 'name' => 'Hebrews'],
        ['code' => 'JAS', 'name' => 'James'],
        ['code' => '1PE', 'name' => '1 Peter'],
        ['code' => '2PE', 'name' => '2 Peter'],
        ['code' => '1JN', 'name' => '1 John'],
        ['code' => '2JN', 'name' => '2 John'],
        ['code' => '3JN', 'name' => '3 John'],
        ['code' => 'JUD', 'name' => 'Jude'],
        ['code' => 'REV', 'name' => 'Revelation'],
    ];

    protected array $bibleStudyBookNamesRw = [
        'GEN' => 'Itangiriro',
        'EXO' => 'Kuva',
        'LEV' => 'Abalewi',
        'NUM' => 'Kubara',
        'DEU' => 'Gutegeka kwa kabiri',
        'JOS' => 'Yosuwa',
        'JDG' => 'Abacamanza',
        'RUT' => 'Rusi',
        '1SA' => '1 Samweli',
        '2SA' => '2 Samweli',
        '1KI' => '1 Abami',
        '2KI' => '2 Abami',
        '1CH' => '1 Ibyo ku Ngoma',
        '2CH' => '2 Ibyo ku Ngoma',
        'EZR' => 'Ezira',
        'NEH' => 'Nehemiya',
        'EST' => 'Esiteri',
        'JOB' => 'Yobu',
        'PSA' => 'Zaburi',
        'PRO' => 'Imigani',
        'ECC' => 'Umubwiriza',
        'SNG' => 'Indirimbo ya Salomo',
        'ISA' => 'Yesaya',
        'JER' => 'Yeremiya',
        'LAM' => 'Amaganya',
        'EZK' => 'Ezekiyeli',
        'DAN' => 'Daniyeli',
        'HOS' => 'Hoseya',
        'JOL' => 'Yoweli',
        'AMO' => 'Amosi',
        'OBA' => 'Obadiya',
        'JON' => 'Yona',
        'MIC' => 'Mika',
        'NAM' => 'Nahumu',
        'HAB' => 'Habakuki',
        'ZEP' => 'Zefaniya',
        'HAG' => 'Hagayi',
        'ZEC' => 'Zekariya',
        'MAL' => 'Malaki',
        'MAT' => 'Matayo',
        'MRK' => 'Mariko',
        'LUK' => 'Luka',
        'JHN' => 'Yohana',
        'ACT' => 'Ibyakozwe n\'Intumwa',
        'ROM' => 'Abaroma',
        '1CO' => '1 Abakorinto',
        '2CO' => '2 Abakorinto',
        'GAL' => 'Abagalatiya',
        'EPH' => 'Abefeso',
        'PHP' => 'Abafilipi',
        'COL' => 'Abakolosayi',
        '1TH' => '1 Abatesalonike',
        '2TH' => '2 Abatesalonike',
        '1TI' => '1 Timoteyo',
        '2TI' => '2 Timoteyo',
        'TIT' => 'Tito',
        'PHM' => 'Filemoni',
        'HEB' => 'Abaheburayo',
        'JAS' => 'Yakobo',
        '1PE' => '1 Petero',
        '2PE' => '2 Petero',
        '1JN' => '1 Yohana',
        '2JN' => '2 Yohana',
        '3JN' => '3 Yohana',
        'JUD' => 'Yuda',
        'REV' => 'Ibyahishuwe',
    ];

    protected ?array $bibleStudyChapterCounts = null;

    protected function actionPlanView(): string
    {
        return 'modules.intercession.partials.actions';
    }

    public function index()
{
    // Forms data
    $stats = [
        'total_forms' => 0,
        'my_attempts' => 0,
        'best_avg' => 0,
    ];
    $availableForms = collect();
    $mySubmissions = collect();
    $allForms = collect();
    
    try {
        $canManageForms = auth()->user()->isSuperAdmin()
            || auth()->user()->canAccess('intercession', 'manage-forms');
        $publishedFormsQuery = Form::query()
            ->where('is_active', true)
            ->whereRaw("(settings::jsonb ->> 'is_published') = 'true'");

        $stats['total_forms'] = $canManageForms
            ? Form::count()
            : (clone $publishedFormsQuery)->count();
        $stats['my_attempts'] = FormSubmission::where('user_id', auth()->id())->count() ?? 0;
        $availableForms = $publishedFormsQuery->latest()->get();
        $mySubmissions = FormSubmission::where('user_id', auth()->id())
            ->with('form')
            ->latest('submitted_at')
            ->get();
        $visibleScores = $mySubmissions->filter(function ($submission) {
            $releaseGrade = $submission->form?->settings['release_grade'] ?? 'immediately';
            $isReleased = !empty($submission->released_at) || !empty($submission->is_released);

            return $releaseGrade === 'immediately'
                || ($releaseGrade === 'later' && $isReleased);
        })->pluck('score')->filter(fn ($score) => $score !== null);
        $stats['best_avg'] = $visibleScores->max() ?? 0;
        $allForms = Form::withCount('submissions')->latest()->get();
    } catch (\Exception $e) {
        // Table doesn't exist yet
    }
    
    $users = collect();
    try {
        $users = User::all();
    } catch (\Exception $e) {
        // Table doesn't exist yet
    }

    $bibleStudyVersions = $this->bibleStudyVersions;
    $bibleStudyBooks = $this->bibleStudyBooks;
    $bibleStudyBookNamesRw = $this->bibleStudyBookNamesRw;
    $bibleStudyChapterCounts = $this->buildBibleStudyChapterCounts();
    $bibleStudyDefaultCompare = $this->bibleStudyDefaultCompare;
    
    return view('modules.intercession.index', compact(
        'stats', 
        'availableForms', 
        'mySubmissions', 
        'allForms',
        'users', 
        'bibleStudyVersions',
        'bibleStudyBooks',
        'bibleStudyBookNamesRw',
        'bibleStudyChapterCounts',
        'bibleStudyDefaultCompare'
    ));
}

    public function bibleStudyChapter(Request $request)
    {
        $validated = $request->validate([
            'version' => 'required|string',
            'compare' => 'nullable|string',
            'book' => 'required|string',
            'chapter' => 'required|integer|min:1',
        ]);

        $primary = $this->normalizeBibleVersion($validated['version']);
        $compare = !empty($validated['compare']) ? $this->normalizeBibleVersion($validated['compare']) : null;
        $book = $this->normalizeBibleBook($validated['book']);
        $chapter = (int) $validated['chapter'];
        $maxChapter = $this->getBibleBookChapterCount($book);

        if (!$primary || !$book) {
            return response()->json(['success' => false, 'message' => 'Invalid Bible selection.'], 422);
        }

        if ($maxChapter !== null && $chapter > $maxChapter) {
            return response()->json([
                'success' => false,
                'message' => sprintf('%s only has %d chapter%s.', $book, $maxChapter, $maxChapter === 1 ? '' : 's'),
            ], 422);
        }

        try {
            $primaryChapter = $this->fetchBibleChapter($primary, $book, $chapter);
            $compareChapter = $compare && $compare['code'] !== $primary['code']
                ? $this->fetchBibleChapter($compare, $book, $chapter)
                : null;

            return response()->json([
                'success' => true,
                'primary' => $primaryChapter,
                'compare' => $compareChapter,
                'book' => $book,
                'chapter' => $chapter,
            ]);
        } catch (\Throwable $e) {
            Log::error('Bible study chapter load failed', [
                'error' => $e->getMessage(),
                'version' => $validated['version'] ?? null,
                'compare' => $validated['compare'] ?? null,
                'book' => $validated['book'] ?? null,
                'chapter' => $validated['chapter'] ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Unable to load the selected chapter right now.',
            ], 500);
        }
    }
    
    public function actionPlans(Request $request)
    {
        return $this->actionPlanIndex($request);
    }

    public function storeActionPlan(Request $request)
    {
        return $this->actionPlanStore($request);
    }

    public function updateActionPlanStatus(Request $request, $id)
    {
        return $this->actionPlanUpdateStatus($request, $id);
    }

    public function deleteActionPlan($id)
    {
        return $this->actionPlanDestroy($id);
    }
    
    /**
 * Edit action plan - get plan data for editing
 */
/**
 * Edit action plan - get plan data for editing
 */
public function editActionPlan($id)
{
    return $this->actionPlanEdit($id);
}
    
    /**
 * Update action plan
 */
/**
 * Update action plan
 */
public function updateActionPlan(Request $request, $id)
{
    return $this->actionPlanUpdate($request, $id);
}

public function addTask(Request $request, $planId)
{
    return $this->actionPlanAddTask($request, $planId);
}

public function updateTask(Request $request, $taskId)
{
    return $this->actionPlanUpdateTask($request, $taskId);
}

public function deleteTask($taskId)
{
    return $this->actionPlanDeleteTask($taskId);
}
/**
 * Update task status (started/in-progress/completed)
 */
    public function updateTaskStatus(Request $request, $id)
    {
        try {
            $status = $request->status;
            $updateData = ['status' => $status, 'updated_at' => now()];
        
        if ($status === 'in-progress' && !DB::table('action_plan_tasks')->where('id', $id)->value('started_at')) {
            $updateData['started_at'] = now();
        }
        if ($status === 'completed' && !DB::table('action_plan_tasks')->where('id', $id)->value('completed_at')) {
            $updateData['completed_at'] = now();
        }
        if ($status === 'pending') {
            $updateData['started_at'] = null;
            $updateData['completed_at'] = null;
        }
        
        DB::table('action_plan_tasks')->where('id', $id)->update($updateData);
        
        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    protected function normalizeBibleVersion(string $version): ?array
    {
        return $this->bibleStudyVersions[strtolower(trim($version))] ?? null;
    }

    protected function normalizeBibleBook(string $book): ?string
    {
        $book = strtoupper(trim($book));

        foreach ($this->bibleStudyBooks as $candidate) {
            if ($candidate['code'] === $book) {
                return $book;
            }
        }

        return null;
    }

    protected function fetchBibleChapter(array $version, string $book, int $chapter): array
    {
        $cacheKey = sprintf('bible-study-local-v3:%s:%s:%d', $version['code'], $book, $chapter);

        return Cache::remember($cacheKey, now()->addHours(12), function () use ($version, $book, $chapter) {
            try {
                return $this->fetchBibleChapterFromLocalXml($version, $book, $chapter);
            } catch (\Throwable $localError) {
                $url = sprintf('https://www.bible.com/bible/%d/%s.%d.%s', $version['id'], $book, $chapter, $version['code']);

                try {
                    return $this->extractBibleChapterFromHtml($this->fetchBibleChapterHtml($url), $version, $book, $chapter);
                } catch (\Throwable $httpError) {
                $browserHtml = $this->fetchBibleChapterHtmlWithBrowser($url);

                if (is_string($browserHtml) && trim($browserHtml) !== '') {
                    try {
                        return $this->extractBibleChapterFromHtml($browserHtml, $version, $book, $chapter);
                    } catch (\Throwable $browserError) {
                        Log::warning('Bible study browser fallback failed', [
                            'url' => $url,
                            'error' => $browserError->getMessage(),
                        ]);
                    }
                }

                    Log::warning('Bible study local file fallback failed', [
                        'version' => $version['code'],
                        'book' => $book,
                        'chapter' => $chapter,
                        'error' => $localError->getMessage(),
                    ]);

                    throw $httpError;
                }
            }
        });
    }

    protected function fetchBibleChapterFromLocalXml(array $version, string $book, int $chapter): array
    {
        $filePath = $this->resolveBibleLocalFilePath($version);
        if (!$filePath || !is_file($filePath)) {
            throw new \RuntimeException('Local Bible file not found.');
        }

        $xmlContent = file_get_contents($filePath);
        if ($xmlContent === false || trim($xmlContent) === '') {
            throw new \RuntimeException('Local Bible file could not be read.');
        }

        return $this->extractBibleChapterFromLocalXml($xmlContent, $version, $book, $chapter);
    }

    protected function resolveBibleLocalFilePath(array $version): ?string
    {
        $fileName = $this->bibleLocalVersionFiles[strtolower($version['code'] ?? '')] ?? null;
        if (!$fileName) {
            return null;
        }

        $baseDir = rtrim((string) env('BIBLE_LOCAL_PATH', $this->bibleLocalDirectory), "\\/");
        return $baseDir . DIRECTORY_SEPARATOR . $fileName;
    }

    protected function extractBibleChapterFromLocalXml(string $xmlContent, array $version, string $book, int $chapter): array
    {
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xmlContent);
        if (!$xml instanceof \SimpleXMLElement) {
            throw new \RuntimeException('Unable to parse local Bible XML.');
        }

        $bookIndex = $this->getBibleBookIndex($book);
        if (!$bookIndex) {
            throw new \RuntimeException('Unknown Bible book.');
        }

        $bookNode = $this->getLocalBibleBookNode($xml, $bookIndex);
        if (!$bookNode) {
            throw new \RuntimeException('Bible book not found in local XML.');
        }

        $chapterNode = $this->getLocalBibleChapterNode($bookNode, $chapter);
        if (!$chapterNode) {
            throw new \RuntimeException('Bible chapter not found in local XML.');
        }

        $contentHtml = $this->renderLocalBibleChapterHtml($chapterNode);
        $bookDisplayName = $this->getBibleBookDisplayName($book, $version['code']);

        return [
            'version' => $version,
            'book' => $book,
            'chapter' => $chapter,
            'title' => $bookDisplayName . ' ' . $chapter,
            'bookAndChapter' => $bookDisplayName . ' ' . $chapter,
            'contentHtml' => $contentHtml,
            'rawContent' => $contentHtml,
            'previous' => null,
            'next' => null,
        ];
    }

    protected function getLocalBibleBookNode(\SimpleXMLElement $xml, int $bookIndex): ?\SimpleXMLElement
    {
        $bookNodes = [];

        foreach ($xml->xpath('/bible/testament/book') ?: [] as $node) {
            $bookNodes[] = $node;
        }

        if (empty($bookNodes)) {
            foreach ($xml->xpath('/bible/b') ?: [] as $node) {
                $bookNodes[] = $node;
            }
        }

        if (empty($bookNodes)) {
            foreach ($xml->xpath('/XMLBIBLE/BIBLEBOOK') ?: [] as $node) {
                $bookNodes[] = $node;
            }
        }

        return $bookNodes[$bookIndex - 1] ?? null;
    }

    protected function getLocalBibleChapterNode(\SimpleXMLElement $bookNode, int $chapter): ?\SimpleXMLElement
    {
        $chapterNodes = [];

        foreach ($bookNode->chapter ?: [] as $node) {
            $chapterNodes[] = $node;
        }

        if (empty($chapterNodes)) {
            foreach ($bookNode->c ?: [] as $node) {
                $chapterNodes[] = $node;
            }
        }

        if (empty($chapterNodes)) {
            foreach ($bookNode->CHAPTER ?: [] as $node) {
                $chapterNodes[] = $node;
            }
        }

        foreach ($chapterNodes as $node) {
            $number = (int) ($node['number'] ?? $node['n'] ?? 0);
            if ($number === 0) {
                $number = (int) ($node['cnumber'] ?? 0);
            }
            if ($number === $chapter) {
                return $node;
            }
        }

        return null;
    }

    protected function renderLocalBibleChapterHtml(\SimpleXMLElement $chapterNode): string
    {
        $html = '<div class="chapter">';

        $verseNodes = [];
        foreach ($chapterNode->verse ?: [] as $node) {
            $verseNodes[] = $node;
        }
        if (empty($verseNodes)) {
            foreach ($chapterNode->v ?: [] as $node) {
                $verseNodes[] = $node;
            }
        }

        if (empty($verseNodes)) {
            foreach ($chapterNode->VERS ?: [] as $node) {
                $verseNodes[] = $node;
            }
        }

        foreach ($verseNodes as $verseNode) {
            $verseNumber = (string) ($verseNode['number'] ?? $verseNode['n'] ?? '');
            if ($verseNumber === '') {
                $verseNumber = (string) ($verseNode['vnumber'] ?? '');
            }
            $verseText = trim((string) $verseNode);
            if ($verseText === '') {
                continue;
            }

            $html .= '<div class="verse"><span class="label">' . e($verseNumber) . '</span> <span class="content">' . e($verseText) . '</span></div>';
        }

        $html .= '</div>';

        return $html;
    }

    protected function getBibleBookDisplayName(string $bookCode, string $versionCode): string
    {
        $bookCode = strtoupper($bookCode);
        $versionCode = strtoupper($versionCode);

        if (in_array($versionCode, ['BYSB', 'BIR'], true)) {
            return $this->bibleStudyBookNamesRw[$bookCode] ?? $bookCode;
        }

        $book = collect($this->bibleStudyBooks)->firstWhere('code', $bookCode);

        return data_get($book, 'name', $bookCode);
    }

    protected function buildBibleStudyChapterCounts(): array
    {
        if ($this->bibleStudyChapterCounts !== null) {
            return $this->bibleStudyChapterCounts;
        }

        $counts = [];
        $filePath = $this->resolveBibleLocalFilePath($this->bibleStudyVersions['bysb']);

        if (!is_string($filePath) || !is_file($filePath)) {
            return $this->bibleStudyChapterCounts = $counts;
        }

        $xmlContent = file_get_contents($filePath);
        if ($xmlContent === false || trim($xmlContent) === '') {
            return $this->bibleStudyChapterCounts = $counts;
        }

        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($xmlContent);
        if (!$xml instanceof \SimpleXMLElement) {
            return $this->bibleStudyChapterCounts = $counts;
        }

        $bookNodes = $xml->xpath('/bible/testament/book') ?: [];
        if (empty($bookNodes)) {
            $bookNodes = $xml->xpath('/bible/b') ?: [];
        }

        foreach ($this->bibleStudyBooks as $index => $book) {
            $node = $bookNodes[$index] ?? null;
            if (!$node) {
                continue;
            }

            $chapterCount = count($node->chapter ?: []);
            if ($chapterCount === 0) {
                $chapterCount = count($node->c ?: []);
            }
            if ($chapterCount === 0) {
                $chapterCount = count($node->CHAPTER ?: []);
            }

            if ($chapterCount > 0) {
                $counts[$book['code']] = $chapterCount;
            }
        }

        return $this->bibleStudyChapterCounts = $counts;
    }

    protected function getBibleBookChapterCount(?string $bookCode): ?int
    {
        $bookCode = strtoupper(trim((string) $bookCode));
        $counts = $this->buildBibleStudyChapterCounts();

        return $counts[$bookCode] ?? null;
    }

    protected function getBibleBookIndex(string $book): ?int
    {
        $index = array_search(strtoupper(trim($book)), array_column($this->bibleStudyBooks, 'code'), true);

        return $index === false ? null : $index + 1;
    }

    protected function fetchBibleChapterHtml(string $url): string
    {
        $response = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0 Safari/537.36',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language' => 'en-US,en;q=0.9',
        ])->connectTimeout(15)->timeout(60)->retry(2, 1000)->get($url);

        if (!$response->successful()) {
            throw new \RuntimeException('Unable to fetch Bible chapter page.');
        }

        return $response->body();
    }

    protected function extractBibleChapterFromHtml(string $html, array $version, string $book, int $chapter): array
    {
        $nextData = $this->extractNextData($html);
        $pageProps = $nextData['props']['pageProps'] ?? [];
        $chapterInfo = $pageProps['chapterInfo'] ?? null;

        if (!$chapterInfo || empty($chapterInfo['content'])) {
            throw new \RuntimeException('Bible chapter content was not found.');
        }

        $contentHtml = $this->sanitizeBibleChapterHtml($chapterInfo['content']);

        return [
            'version' => $version,
            'book' => $book,
            'chapter' => $chapter,
            'title' => $chapterInfo['reference']['title'] ?? ($pageProps['referenceTitle']['title'] ?? null),
            'bookAndChapter' => $chapterInfo['reference']['bookAndChapter'] ?? ($pageProps['referenceTitle']['bookAndChapter'] ?? null),
            'contentHtml' => $contentHtml,
            'rawContent' => $chapterInfo['content'],
            'previous' => $chapterInfo['previous'] ?? null,
            'next' => $chapterInfo['next'] ?? null,
        ];
    }

    protected function fetchBibleChapterHtmlWithBrowser(string $url): ?string
    {
        $browser = $this->resolveHeadlessBrowserExecutable();
        if (!$browser) {
            return null;
        }

        $profileDir = storage_path('app/bible-browser-profile-' . Str::uuid());
        if (!File::exists($profileDir)) {
            File::makeDirectory($profileDir, 0755, true);
        }

        try {
            $process = new Process([
                $browser,
                '--headless',
                '--disable-gpu',
                '--no-first-run',
                '--no-default-browser-check',
                '--dump-dom',
                '--user-data-dir=' . $profileDir,
                $url,
            ]);
            $process->setTimeout(120);
            $process->run();

            if (!$process->isSuccessful()) {
                Log::warning('Bible study browser fetch failed', [
                    'url' => $url,
                    'error' => trim($process->getErrorOutput()) ?: trim($process->getOutput()),
                ]);

                return null;
            }

            $html = trim($process->getOutput());
            return $html !== '' ? $html : null;
        } finally {
            File::deleteDirectory($profileDir);
        }
    }

    protected function resolveHeadlessBrowserExecutable(): ?string
    {
        if (PHP_OS_FAMILY === 'Windows') {
            foreach ([
                'C:\\Program Files (x86)\\Google\\Chrome\\Application\\chrome.exe',
                'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
                'C:\\Program Files (x86)\\Microsoft\\Edge\\Application\\msedge.exe',
                'C:\\Program Files\\Microsoft\\Edge\\Application\\msedge.exe',
            ] as $path) {
                if (is_file($path)) {
                    return $path;
                }
            }

            return null;
        }

        $finder = new ExecutableFinder();

        return $finder->find('google-chrome')
            ?: $finder->find('chrome')
            ?: $finder->find('chromium')
            ?: $finder->find('chromium-browser')
            ?: $finder->find('msedge');
    }

    protected function extractNextData(string $html): array
    {
        $marker = 'id="__NEXT_DATA__"';
        $markerPos = strpos($html, $marker);
        if ($markerPos === false) {
            throw new \RuntimeException('Unable to extract page data.');
        }

        $scriptStart = strrpos(substr($html, 0, $markerPos), '<script');
        if ($scriptStart === false) {
            throw new \RuntimeException('Unable to extract page data.');
        }

        $scriptEnd = strpos($html, '</script>', $markerPos);
        if ($scriptEnd === false) {
            throw new \RuntimeException('Unable to extract page data.');
        }

        $scriptTag = substr($html, $scriptStart, $scriptEnd - $scriptStart);
        $openTagEnd = strpos($scriptTag, '>');
        if ($openTagEnd === false) {
            throw new \RuntimeException('Unable to extract page data.');
        }

        $json = substr($scriptTag, $openTagEnd + 1);
        $json = html_entity_decode($json, ENT_QUOTES | ENT_HTML5);
        $data = json_decode($json, true);

        if (!is_array($data)) {
            throw new \RuntimeException('Invalid chapter data returned by the source site.');
        }

        return $data;
    }

    protected function sanitizeBibleChapterHtml(string $html): string
    {
        $html = preg_replace('#<script\b[^>]*>.*?</script>#is', '', $html);
        $html = preg_replace('#<style\b[^>]*>.*?</style>#is', '', $html);
        $html = preg_replace('/\sdata-[a-z0-9_-]+="[^"]*"/i', '', $html);

        return trim($html);
    }
}
