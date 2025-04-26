<?php
session_start();

// Full array of quiz questions
$quiz_questions = [
    [
        'id' => 1,
        'question' => 'What is the capital of France?',
        'options' => ['A' => 'Berlin', 'B' => 'Madrid', 'C' => 'Paris', 'D' => 'Rome']
    ],
    [
        'id' => 2,
        'question' => 'Who wrote the national anthem of India?',
        'options' => ['A' => 'Bankim Chandra Chatterjee', 'B' => 'Rabindranath Tagore', 'C' => 'Sarojini Naidu', 'D' => 'Mahatma Gandhi']
    ],
    [
        'id' => 3,
        'question' => 'Which planet is known as the Red Planet?',
        'options' => ['A' => 'Earth', 'B' => 'Venus', 'C' => 'Mars', 'D' => 'Jupiter']
    ],
    [
        'id' => 4,
        'question' => 'Who was the first president of India?',
        'options' => ['A' => 'Dr Rajendra Prasad', 'B' => 'Mahatma Gandhi', 'C' => 'Jawaharlal Nehru', 'D' => 'Sarojini Naidu']
    ],
    [
        'id' => 5,
        'question' => 'What is the longest river in the world?',
        'options' => ['A' => 'Amazon River', 'B' => 'Nile River', 'C' => 'Mekong River', 'D' =>'Congo River']
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Quiz - Edusnap</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center px-4">
  <div class="w-full max-w-3xl bg-white p-8 rounded-lg shadow-lg">
    <h2 class="text-3xl font-bold text-center mb-6 text-blue-700">General Knowledge Quiz</h2>
    <form method="POST" action="quiz_result.php">
      <?php foreach ($quiz_questions as $index => $quiz_item): ?>
        <div class="mb-6">
          <label class="block text-lg font-medium text-gray-800 mb-2">
            <?php echo ($index + 1) . '. ' . $quiz_item['question']; ?>
          </label>
          <?php foreach ($quiz_item['options'] as $key => $value): ?>
            <div class="flex items-center mb-2">
              <input type="radio" name="quiz_<?php echo $quiz_item['id']; ?>" value="<?php echo $key; ?>" required class="mr-2">
              <label class="text-gray-700"><?php echo "$key. $value"; ?></label>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>
      <div class="text-center">
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 transition">
          Submit Quiz
        </button>
      </div>
    </form>
  </div>
</body>
</html>

