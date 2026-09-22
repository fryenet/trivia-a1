<?php
require_once __DIR__.'/functions.php';
require_login();

$game_id = (int)($_GET['game_id'] ?? 0);
$game = $db->queryOne("SELECT * FROM games WHERE id=? AND user_id=?", [$game_id, current_user()['id']]);
if (!$game) { header('Location: home.php'); exit; }

$trivia_id = (int)$game['trivia_id'];
$available_q = (int)$db->queryValue("SELECT COUNT(*) FROM questions WHERE trivia_id=?", [$trivia_id]);
$total_q = min(10, $available_q);

// Precompute the list of question IDs for this session (first 10 by id)
$qids = $db->query("SELECT id FROM questions WHERE trivia_id=? ORDER BY id ASC LIMIT $total_q", [$trivia_id]);
$qid_list = array_map(fn($r)=> (int)$r['id'], $qids);

// get answered count within those 10
$answered = (int)$db->queryValue("
    SELECT COUNT(*) FROM game_answers 
    WHERE game_id=? AND question_id IN (".implode(',', $qid_list ?: [0]).")
", [$game_id]);

// handle post (save answer)
if ($_SERVER['REQUEST_METHOD']==='POST'){
    verify_csrf();
    $qid = (int)$_POST['question_id'];
    $choice = strtoupper(substr($_POST['choice'] ?? '',0,1));
    $correct_choice = $db->queryValue("SELECT correct_choice FROM questions WHERE id=?", [$qid]);
    if (in_array($choice, ['A','B','C','D'])) {
        $is_correct = ($choice === $correct_choice) ? 1 : 0;
        // Upsert-like: avoid double answers
        $exists = (int)$db->queryValue("SELECT COUNT(*) FROM game_answers WHERE game_id=? AND question_id=?", [$game_id,$qid]);
        if (!$exists && in_array($qid, $qid_list)){
            $db->insert('game_answers', [
                'game_id'=>$game_id,
                'question_id'=>$qid,
                'selected_choice'=>$choice,
                'is_correct'=>$is_correct
            ]);
        }
    }
    $answered++;
    if ($answered >= $total_q){
        // compute score and bucket
        list($score, $bucket) = compute_score_and_bucket($game_id);
        header('Location: results.php?game_id='.$game_id); exit;
    }
}

// Determine next question among the selected 10
$next_q = null;
foreach ($qid_list as $qid){
    $exists = (int)$db->queryValue("SELECT COUNT(*) FROM game_answers WHERE game_id=? AND question_id=?", [$game_id,$qid]);
    if (!$exists){
        $next_q = $db->queryOne("SELECT * FROM questions WHERE id=?", [$qid]);
        break;
    }
}

// if no more questions, compute & redirect
if (!$next_q){
    list($score, $bucket) = compute_score_and_bucket($game_id);
    header('Location: results.php?game_id='.$game_id); exit;
}
$progress = $answered + 1;
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Question <?php echo $progress; ?></title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="page">
    <div class="meta">Question <?php echo $progress; ?> of <?php echo $total_q; ?></div>
    <div class="card">
      <strong><?php echo h($next_q['question_text']); ?></strong>
      <form method="post" style="margin-top:10px">
        <?php csrf_field(); ?>
        <input type="hidden" name="question_id" value="<?php echo (int)$next_q['id']; ?>">
        <label><input type="radio" name="choice" value="A" required> <?php echo h($next_q['choice_a']); ?></label><br>
        <label><input type="radio" name="choice" value="B"> <?php echo h($next_q['choice_b']); ?></label><br>
        <label><input type="radio" name="choice" value="C"> <?php echo h($next_q['choice_c']); ?></label><br>
        <label><input type="radio" name="choice" value="D"> <?php echo h($next_q['choice_d']); ?></label><br>
        <button class="btn" type="submit">Next</button>
      </form>
    </div>
  </div>
</body>
</html>
