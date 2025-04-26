<?php
session_start();

// Define correct answers for the hardcoded quiz
$correct_answers = [
    1 => 'C', // Paris
    2 => 'B', // Rabindranath Tagore
    3 => 'C', // Mars
    4 => 'A', // Dr Rajendra Prasad
    5 => 'B', // Nile River
];

$score = 0;
$total = count($correct_answers);
$results = [];

foreach ($correct_answers as $id => $correct_option) {
    $user_answer = $_POST["quiz_$id"] ?? null;
    $is_correct = $user_answer === $correct_option;
    if ($is_correct) {
        $score++;
    }

    $results[] = [
        'id' => $id,
        'user' => $user_answer,
        'correct' => $correct_option,
        'is_correct' => $is_correct
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Quiz Results</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

  <div class="container mx-auto py-8">
    <div class="bg-white p-6 rounded-md shadow-lg">
      <h2 class="text-2xl font-bold mb-4 text-green-700">Quiz Results</h2>
      <p class="mb-4 text-lg">You scored <span class="font-bold"><?php echo $score; ?></span> out of <?php echo $total; ?>.</p>

      <div class="space-y-4">
        <?php foreach ($results as $res) { ?>
          <div class="p-4 bg-gray-50 rounded border <?php echo $res['is_correct'] ? 'border-green-400' : 'border-red-400'; ?>">
            <p class="text-gray-800">
              <strong>Question <?php echo $res['id']; ?>:</strong><br>
              Your Answer: <span class="<?php echo $res['is_correct'] ? 'text-green-600' : 'text-red-600'; ?>"><?php echo $res['user'] ?? 'No answer'; ?></span><br>
              Correct Answer: <span class="text-green-600"><?php echo $res['correct']; ?></span>
            </p>
          </div>
        <?php } ?>
      </div>

      <div class="mt-6">
        <a href="quiz.php" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Try Again</a>
        <a href="index.php" class="ml-2 text-blue-600 hover:underline">Go to Home</a>
      </div>
    </div>
  </div>

</body>
</html>
