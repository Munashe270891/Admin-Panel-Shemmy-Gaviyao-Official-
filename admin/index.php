<?php
require_once '../config.php';
require_once 'auth.php';

// Fetch all existing entries for the management tab table
$audioTracks = $pdo->query("SELECT id, title, topic AS subtitle, 'Audio' AS type FROM audio_tracks ORDER BY id DESC")->fetchAll();
$videos = $pdo->query("SELECT id, title, 'YouTube Video' AS subtitle, 'Video' AS type FROM videos ORDER BY id DESC")->fetchAll();
$devotionals = $pdo->query("SELECT id, title, badge AS subtitle, 'Devotion' AS type FROM devotionals ORDER BY id DESC")->fetchAll();
$books = $pdo->query("SELECT id, title, 'Published Book' AS subtitle, 'Book' AS type FROM books ORDER BY id DESC")->fetchAll();

$allContent = array_merge($audioTracks, $videos, $devotionals, $books);
$activeTab = $_GET['tab'] ?? 'audio';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shemmy Gaviyawo Official Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #1a1a2e;
            --accent-color: #d4af37;
            --bg-light: #f4f6f9;
            --white: #ffffff;
            --danger: #e74c3c;
            --success: #2ecc71;
            --gray: #7f8c8d;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-light);
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }
        .sidebar {
            width: 260px;
            background-color: var(--primary-color);
            color: var(--white);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.3s ease;
            z-index: 1000;
        }
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-header h2 {
            font-size: 1.2rem;
            color: var(--accent-color);
            margin: 0;
        }
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
            flex-grow: 1;
            overflow-y: auto;
        }
        .sidebar-menu li a {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: var(--white);
            text-decoration: none;
            transition: 0.3s;
        }
        .sidebar-menu li a:hover, .sidebar-menu li.active a {
            background-color: rgba(212, 175, 55, 0.2);
            border-left: 4px solid var(--accent-color);
        }
        .sidebar-menu li a i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
        }
        .main-content {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            width: 100%;
        }
        .top-navbar {
            background-color: var(--white);
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            gap: 10px;
            flex-wrap: wrap;
        }
        .top-navbar h3 {
            font-size: 1.1rem;
            margin: 0;
            color: var(--primary-color);
        }
        .admin-badge {
            background: var(--bg-light);
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            color: #333;
            font-weight: 600;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .menu-toggle-btn {
            background: none;
            border: none;
            font-size: 1.3rem;
            color: var(--primary-color);
            cursor: pointer;
            display: none;
        }
        .container {
            padding: 20px;
            max-width: 1000px;
            margin: auto;
            width: 100%;
            box-sizing: border-box;
        }
        .panel-section {
            display: none;
            background: var(--white);
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
            margin-bottom: 30px;
        }
        .panel-section.active {
            display: block;
        }
        h2.section-title {
            margin-top: 0;
            color: var(--primary-color);
            border-bottom: 2px solid var(--bg-light);
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-size: 1.3rem;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #333;
            font-size: 0.95rem;
        }
        .form-group input[type="text"],
        .form-group input[type="file"],
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            box-sizing: border-box;
            font-size: 1rem;
            background: #fff;
        }
        .form-group textarea {
            resize: vertical;
            height: 120px;
        }
        .btn-submit {
            background-color: var(--accent-color);
            color: var(--primary-color);
            border: none;
            padding: 12px 25px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s;
            width: 100%;
        }
        .btn-submit:hover {
            opacity: 0.9;
        }
        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }
        .content-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            min-width: 500px;
        }
        .content-table th, .content-table td {
            padding: 12px 10px;
            text-align: left;
            border-bottom: 1px solid #eee;
            font-size: 0.9rem;
        }
        .content-table th {
            background-color: #f8f9fa;
            color: var(--primary-color);
        }
        .action-btns button {
            padding: 6px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8rem;
        }
        .btn-delete { background-color: var(--danger); color: white; }
        @media (max-width: 768px) {
            body { flex-direction: column; }
            .sidebar {
                position: fixed;
                height: 100%;
                transform: translateX(-100%);
            }
            .sidebar.mobile-open { transform: translateX(0); }
            .menu-toggle-btn { display: block; }
        }
    </style>
</head>
<body>

    <!-- Sidebar Navigation -->
    <div class="sidebar" id="appSidebar">
        <div>
            <div class="sidebar-header">
                <h2>Admin Panel</h2>
                <p style="font-size: 0.8rem; color: #aaa; margin: 5px 0 0 0;">Shemmy Gaviyawo Official</p>
            </div>
            <ul class="sidebar-menu">
                <li class="<?php echo $activeTab === 'audio' ? 'active' : ''; ?>"><a href="#" onclick="switchTab('audio')"><i class="fas fa-podcast"></i> Upload Audio Sermon</a></li>
                <li class="<?php echo $activeTab === 'video' ? 'active' : ''; ?>"><a href="#" onclick="switchTab('video')"><i class="fas fa-video"></i> Upload YouTube Video</a></li>
                <li class="<?php echo $activeTab === 'devotion' ? 'active' : ''; ?>"><a href="#" onclick="switchTab('devotion')"><i class="fas fa-book-open"></i> Upload Devotion</a></li>
                <li class="<?php echo $activeTab === 'book' ? 'active' : ''; ?>"><a href="#" onclick="switchTab('book')"><i class="fas fa-book"></i> Upload New Book</a></li>
                <li class="<?php echo $activeTab === 'manage' ? 'active' : ''; ?>"><a href="#" onclick="switchTab('manage')"><i class="fas fa-list-alt"></i> List of Content</a></li>
            </ul>
        </div>
        <div style="padding: 15px; text-align: center; font-size: 0.75rem; color: #7f8c8d; border-top: 1px solid rgba(255,255,255,0.05);">
            Connected to MySQL DB
        </div>
    </div>

    <!-- Main Content Panel -->
    <div class="main-content">
        <div class="top-navbar">
            <div style="display: flex; align-items: center; gap: 12px;">
                <button class="menu-toggle-btn" onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
                <h3 id="currentDashboardTitle">Upload Audio Sermon</h3>
            </div>
            <div class="admin-badge">
                <span><i class="fas fa-user-shield" style="color: var(--accent-color); margin-right: 5px;"></i> Welcome, Admin</span>
                <a href="logout.php" style="color: var(--danger); text-decoration: none; font-size: 0.8rem; margin-left: 10px;"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>

        <div class="container">

            <!-- 1. Audio Form -->
            <section id="audio-section" class="panel-section <?php echo $activeTab === 'audio' ? 'active' : ''; ?>">
                <h2 class="section-title">Upload New Audio Sermon</h2>
                <form action="actions/save-audio.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Upload Audio File (.mp3)</label>
                        <input type="file" name="audioFile" accept="audio/*" required>
                    </div>
                    <div class="form-group">
                        <label>Upload Thumbnail / Cover Image</label>
                        <input type="file" name="audioImage" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" placeholder="Enter sermon title..." required>
                    </div>
                    <div class="form-group">
                        <label>Subtitle / Series Tag</label>
                        <input type="text" name="subtitle" placeholder="e.g., Sunday Service Series">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" placeholder="Enter a brief description..."></textarea>
                    </div>
                    <button type="submit" class="btn-submit">Publish Audio Sermon</button>
                </form>
            </section>

            <!-- 2. Video Form -->
            <section id="video-section" class="panel-section <?php echo $activeTab === 'video' ? 'active' : ''; ?>">
                <h2 class="section-title">Upload New YouTube Video Link</h2>
                <form action="actions/save-video.php" method="POST">
                    <div class="form-group">
                        <label>YouTube Video Link / URL</label>
                        <input type="text" name="youtubeUrl" placeholder="https://www.youtube.com/watch?v=..." required>
                    </div>
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" placeholder="Enter video title..." required>
                    </div>
                    <button type="submit" class="btn-submit">Publish Video Link</button>
                </form>
            </section>

            <!-- 3. Devotion Form -->
            <section id="devotion-section" class="panel-section <?php echo $activeTab === 'devotion' ? 'active' : ''; ?>">
                <h2 class="section-title">Upload New Daily Devotion</h2>
                <form action="actions/save-devotion.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Upload Accompanying Image</label>
                        <input type="file" name="devotionImage" accept="image/*" required>
                    </div>
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" placeholder="Enter devotion title..." required>
                    </div>
                    <div class="form-group">
                        <label>Subtitle / Theme</label>
                        <input type="text" name="subtitle" placeholder="e.g., Walking in Faith">
                    </div>
                    <div class="form-group">
                        <label>Content / Description</label>
                        <textarea name="description" placeholder="Write or paste your daily devotional text here..." style="height: 180px;" required></textarea>
                    </div>
                    <button type="submit" class="btn-submit">Publish Devotional</button>
                </form>
            </section>

            <!-- 4. Book Form -->
            <section id="book-section" class="panel-section <?php echo $activeTab === 'book' ? 'active' : ''; ?>">
                <h2 class="section-title">Upload New Published Book</h2>
                <form action="actions/save-book.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Upload Book Cover Image</label>
                        <input type="file" name="bookImage" accept="image/*" required>
                    </div>
                    <div class="form-group">
                        <label>Upload PDF File</label>
                        <input type="file" name="bookPdf" accept="application/pdf" required>
                    </div>
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" placeholder="e.g., The Bleeding General" required>
                    </div>
                    <div class="form-group">
                        <label>Description / Blurb</label>
                        <textarea name="description" placeholder="Enter book overview description..." style="height: 140px;" required></textarea>
                    </div>
                    <button type="submit" class="btn-submit">Publish Book Record</button>
                </form>
            </section>

            <!-- 5. Content Management Section -->
            <section id="manage-section" class="panel-section <?php echo $activeTab === 'manage' ? 'active' : ''; ?>">
                <h2 class="section-title">Manage Existing Content</h2>
                <p style="color: var(--gray); font-size: 0.9rem;">View and delete database records instantly.</p>
                
                <div class="table-responsive">
                    <table class="content-table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>Title</th>
                                <th>Subtitle</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($allContent)): ?>
                                <tr><td colspan="4" style="text-align: center; color: #7f8c8d;">No records found in database yet.</td></tr>
                            <?php else: ?>
                                <?php foreach ($allContent as $item): ?>
                                    <tr>
                                        <td><span style="background: #e1f5fe; color: #0288d1; padding: 3px 6px; border-radius: 4px; font-size: 0.75rem;"><?php echo htmlspecialchars($item['type']); ?></span></td>
                                        <td><?php echo htmlspecialchars($item['title']); ?></td>
                                        <td><?php echo htmlspecialchars($item['subtitle']); ?></td>
                                        <td class="action-btns">
                                            <form action="actions/delete-item.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this record?');" style="display:inline;">
                                                <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                                                <input type="hidden" name="type" value="<?php echo strtolower($item['type']); ?>">
                                                <button type="submit" class="btn-delete"><i class="fas fa-trash"></i> Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

        </div>
    </div>

    <script>
        function toggleSidebar() {
            document.getElementById('appSidebar').classList.toggle('mobile-open');
        }

        function switchTab(tabName) {
            document.querySelectorAll('.panel-section').forEach(sec => sec.classList.remove('active'));
            document.querySelectorAll('.sidebar-menu li').forEach(item => item.classList.remove('active'));

            document.getElementById(tabName + '-section').classList.add('active');
            event.currentTarget.parentElement.classList.add('active');
            document.getElementById('appSidebar').classList.remove('mobile-open');

            const titles = {
                'audio': 'Upload Audio Sermon',
                'video': 'Upload YouTube Video Link',
                'devotion': 'Upload Daily Devotion',
                'book': 'Upload New Book',
                'manage': 'List of Content Management'
            };
            document.getElementById('currentDashboardTitle').innerText = titles[tabName];
        }
    </script>
</body>
</html>
