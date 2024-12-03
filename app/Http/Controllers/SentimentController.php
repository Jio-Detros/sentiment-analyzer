<?php

namespace App\Http\Controllers;

use App\Models\Sentiment;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\IOFactory;
use Smalot\PdfParser\Parser;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;

class SentimentController extends Controller
{
    public function index()
    {
        return view('moodify');
    }

    public function create()
    {
        return view('analyze');
    }

    public function store(Request $request)
    {
        $request->validate([
            'input_text' => 'nullable|string',
            'fileInput' => 'nullable|file|mimes:txt,docx,pdf',
        ]);

        $text = $request->input_text ?? ''; // Get input text or set as empty string.

        // Process uploaded file if present
        if ($request->hasFile('fileInput')) {
            $file = $request->file('fileInput');
            $extension = $file->getClientOriginalExtension();

            try {
                if ($extension === 'txt') {
                    $text .= file_get_contents($file->getRealPath());
                } elseif ($extension === 'docx') {
                    $phpWord = IOFactory::load($file->getRealPath());
                    $sections = $phpWord->getSections();
                    foreach ($sections as $section) {
                        $elements = $section->getElements();
                        foreach ($elements as $element) {
                            if (method_exists($element, 'getText')) {
                                $text .= $element->getText() . ' ';
                            }
                        }
                    }
                } elseif ($extension === 'pdf') {
                    $parser = new Parser();
                    $pdf = $parser->parseFile($file->getRealPath());
                    $text .= $pdf->getText();
                }
            } catch (\Exception $e) {
                \Log::error('File parsing error: ' . $e->getMessage());
                return response()->json(['error' => 'Failed to process the uploaded file.'], 500);
            }
        }

        // Check if valid text exists
        if (empty(trim($text))) {
            return response()->json(['error' => 'No valid text found in the input or uploaded file.'], 400);
        }

        $lowercaseText = strtolower($text);

        // Analyze sentiment (adjust the implementation as needed for your analysis logic)
        $analyzer = new \Sentiment\Analyzer();
        $sentimentScores = $analyzer->getSentiment($text);

        $positiveCount = $sentimentScores['pos'];
        $negativeCount = $sentimentScores['neg'];
        $positiveMatches = [];
        $negativeMatches = [];

        // Initialize Azure Blob Storage Client
        $connectionString = 'DefaultEndpointsProtocol=https;AccountName=your_account_name;AccountKey=your_account_key;EndpointSuffix=core.windows.net';
        $blobClient = BlobRestProxy::createBlobService($connectionString);

        try {
            // Retrieve positive and negative words
            $positiveBlob = $blobClient->getBlob('lexicon', 'positive_words.txt');
            $positiveWords = array_map('trim', explode("\n", stream_get_contents($positiveBlob->getContentStream())));

            $negativeBlob = $blobClient->getBlob('lexicon', 'negative_words.txt');
            $negativeWords = array_map('trim', explode("\n", stream_get_contents($negativeBlob->getContentStream())));
        } catch (ServiceException $e) {
            \Log::error('Azure Blob Storage error: ' . $e->getCode() . ' - ' . $e->getMessage());
            return response()->json(['error' => 'Failed to retrieve lexicon files from Azure Blob Storage.'], 500);
        }

        // Count words using lexicon files
        $words = preg_split('/\s+/', $lowercaseText);

        foreach ($words as $word) {
            $cleanWord = trim($word, " \t\n\r\0\x0B.,!?");

            if (!array_key_exists($cleanWord, $sentimentScores)) {
                if (in_array($cleanWord, $positiveWords)) {
                    $positiveCount++;
                    $positiveMatches[] = $cleanWord;
                }
                if (in_array($cleanWord, $negativeWords)) {
                    $negativeCount++;
                    $negativeMatches[] = $cleanWord;
                }
            }
        }

        // Determine overall sentiment and emotion
        $sentimentResult = 'Neutral';
        $sentimentEmotion = 'Neutral';

        if ($positiveCount > $negativeCount) {
            $sentimentResult = 'Positive';
            $sentimentEmotion = 'Happy';
        } elseif ($negativeCount > $positiveCount) {
            $sentimentResult = 'Negative';
            $sentimentEmotion = 'Sad';
        }

        $textFeatures = [];
        if (preg_match('/[A-Z]{2,}/', $text)) {
            $textFeatures[] = 'Contains all-caps';
            if ($sentimentResult === 'Positive') {
                $sentimentEmotion = 'Excited';
            } elseif ($sentimentResult === 'Negative') {
                $sentimentEmotion = 'Angry';
            }
        }

        // Save the sentiment analysis to the database
        Sentiment::create([
            'input_text' => $text,
            'analysis_result' => $sentimentResult,
            'emotion_detected' => $sentimentEmotion,
            'feature_data' => implode('; ', $textFeatures),
            'analysis_date' => now(),
        ]);

        return response()->json([
            'input_text' => $text,
            'positive_count' => $positiveCount,
            'negative_count' => $negativeCount,
            'positive_matches' => $positiveMatches,
            'negative_matches' => $negativeMatches,
            'analysis_result' => $sentimentResult,
            'emotion_detected' => $sentimentEmotion,
            'feature_data' => implode('; ', $textFeatures),
        ]);
    }

    public function history()
    {
        $sentiments = Sentiment::whereNull('deleted_at')->get();
        return view('history', compact('sentiments'));
    }

    public function softDelete($id)
    {
        $sentiment = Sentiment::findOrFail($id);
        $sentiment->delete();

        return response()->json([
            'message' => 'Sentiment deleted successfully.',
            'id' => $id,
        ]);
    }

    public function generateReport(Request $request, $id)
    {
        $sentiment = Sentiment::findOrFail($id);

        $data = [
            'input' => $sentiment->input_text,
            'result' => $sentiment->analysis_result,
            'emotion' => $sentiment->emotion_detected,
            'text_features' => $sentiment->feature_data,
            'date' => $sentiment->analysis_date,
        ];

        if ($request->has('preview') && $request->preview) {
            return view('report', $data)->render(); 
        }

        $pdf = Pdf::loadView('moodify-report', $data);
        $filename = "moodify_sentiment_report_{$sentiment->id}.pdf";
        return $pdf->download($filename);
    }
}
