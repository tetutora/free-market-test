<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>取引完了通知</title>
</head>
<body>
    <h1>取引が完了しました</h1>
    <p>こんにちは、{{ $transaction->product->user->name ?? 'ユーザー' }}様</p>
    <p>お知らせ：あなたの商品「{{ $transaction->product->name ?? '商品名' }}」の取引が完了しました。</p>
</body>
</html>