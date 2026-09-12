<?php
require_once 'config.php';

// Fetch dynamic data for the website views
$devotionals = $pdo->query("SELECT * FROM devotionals ORDER BY publish_date DESC LIMIT 5")->fetchAll();
$videos = $pdo->query("SELECT * FROM videos ORDER BY id DESC LIMIT 6")->fetchAll();
$audioTracks = $pdo->query("SELECT * FROM audio_tracks ORDER BY publish_date DESC LIMIT 6")->fetchAll();
$books = $pdo->query("SELECT * FROM books ORDER BY id DESC LIMIT 4")->fetchAll();
$partner = $pdo->query("SELECT * FROM partnership_content LIMIT 1")->fetch();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Discipleship Nation | Shemmy Gaviyawo Official</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #1a1a2e;
            --accent: #d4af37;
            --bg: #f8f9fa;
            --text: #333;
        }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; background: var(--bg); color: var(--text); }
        header { background: var(--primary); color: white; padding: 20px; text-align: center; }
        header h1 { color: var(--accent); margin: 0 0 5px 0; font-size: 1.8rem; }
        .container { max-width: 1100px; margin: auto; padding: 20px; }
        .section-box { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); margin-bottom: 25px; }
        h2 { color: var(--primary); border-bottom: 2px solid var(--accent); padding-bottom: 8px; margin-top: 0; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; }
        .card { background: #fff; border: 1px solid #e1e8ed; border-radius: 6px; padding: 15px; display: flex; flex-direction: column; justify-content: space-between; }
        .card img { width: 100%; height: 160px; object-fit: cover; border-radius: 4px; margin-bottom: 10px; }
        .card h3 { margin: 10px 0 5px 0; font-size: 1.1rem; color: var(--primary); }
        .card p { font-size: 0.9rem; color: #666; flex-grow: 1; }
        .btn { display: inline-block; background: var(--accent); color: var(--primary); text-decoration: none; padding: 8px 15px; font-weight: bold; border-radius: 4px; text-align: center; margin-top: 10px; }
        footer { text-align: center; padding: 20px; color: #777; font-size: 0.85rem; }
    </style>
</head>
<body>

    <header>
        <h1>Discipleship Nation</h1>
        <p>Shemmy Gaviyawo Official Portal</p>
    </header>

    <div class="container">
        
        <!-- Devotionals Section -->
        <div class="section-box">
            <h2><i class="fas fa-book-open"></i> Daily Devotionals</h2>
            <div class="grid">
                <?php if (empty($devotionals)): ?>
                    <p>No devotionals published yet.</p>
                <?php else: ?>
                    <?php foreach ($devotionals as $dev): ?>
                        <div class="card">
                            <?php if (!empty($dev['image_url'])): ?>
                                <img src="<?php echo htmlspecialchars($dev['image_url']); ?>" alt="Devotion Image">
                            <?php endif; ?>
                            <h3><?php echo htmlspecialchars($dev['title']); ?></h3>
                            <p><?php echo nl2br(htmlspecialchars(substr($dev['content'], 0, 150))); ?>...</p>
                            <span style="font-size: 0.75rem; color: #888; margin-top: 5px;"><?php echo $dev['publish_date']; ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Videos Section -->
        <div class="section-box">
            <h2><i class="fas fa-video"></i> Video Teachings</h2>
            <div class="grid">
                <?php if (empty($videos)): ?>
                    <p>No video links available yet.</p>
                <?php else: ?>
                    <?php foreach ($videos as $vid): ?>
                        <div class="card">
                            <div style="position:relative; padding-bottom:56.16%; height:0; overflow:hidden;">
                                <iframe src="https://www.youtube.com/embed/<?php echo htmlspecialchars($vid['youtube_id']); ?>" style="position:absolute; top:0; left:0; width:100%; height:100%; border:0;" allowfullscreen></iframe>
                            </div>
                            <h3 style="margin-top:15px;"><?php echo htmlspecialchars($vid['title']); ?></h3>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Audio Sermons Section -->
        <div class="section-box">
            <h2><i class="fas fa-podcast"></i> Audio Sermons</h2>
            <div class="grid">
                <?php if (empty($audioTracks)): ?>
                    <p>No audio files uploaded yet.</p>
                <?php else: ?>
                    <?php foreach ($audioTracks as $audio): ?>
                        <div class="card">
                            <?php if (!empty($audio['thumbnail_url'])): ?>
                                <img src="<?php echo htmlspecialchars($audio['thumbnail_url']); ?>" alt="Audio Cover">
                            <?php endif; ?>
                            <h3><?php echo htmlspecialchars($audio['title']); ?></h3>
                            <p style="font-size: 0.8rem; color: #555;">Topic: <?php echo htmlspecialchars($audio['topic']); ?></p>
                            <?php if (!empty($audio['audio_url'])): ?>
                                <audio controls style="width:100%; margin-top:10px;">
                                    <source src="<?php echo htmlspecialchars($audio['audio_url']); ?>" type="audio/mpeg">
                                    Your browser does not support the audio element.
                                </audio>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Books Section -->
        <div class="section-box">
            <h2><i class="fas fa-book"></i> Books & Publications</h2>
            <div class="grid">
                <?php if (empty($books)): ?>
                    <p>No books uploaded yet.</p>
                <?php else: ?>
                    <?php foreach ($books as $bk): ?>
                        <div class="card">
                            <?php if (!empty($bk['cover_image'])): ?>
                                <img src="<?php echo htmlspecialchars($bk['cover_image']); ?>" alt="Book Cover">
                            <?php endif; ?>
                            <h3><?php echo htmlspecialchars($bk['title']); ?></h3>
                            <p><?php echo nl2br(htmlspecialchars($bk['description'])); ?></p>
                            <?php if (!empty($bk['pdf_file'])): ?>
                                <a href="<?php echo htmlspecialchars($bk['pdf_file']); ?>" class="btn" target="_blank"><i class="fas fa-download"></i> Download PDF</a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Partnership Section -->
        <?php if ($partner): ?>
        <div class="section-box" style="background: var(--primary); color: white; text-align: center;">
            <h2 style="color: var(--accent); border-color: rgba(212,175,55,0.3);"><?php echo htmlspecialchars($partner['title']); ?></h2>
            <blockquote style="font-style: italic; color: #d4af37; font-size: 1.1rem;"><?php echo htmlspecialchars($partner['verse']); ?></blockquote>
            <p style="max-width: 700px; margin: 15px auto; line-height: 1.6;"><?php echo nl2br(htmlspecialchars($partner['body'])); ?></p>
        </div>
        <?php endif; ?>

    </div>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Discipleship Nation. Powered by Upnode Technologies.</p>
    </footer>

</body>
</html>
