<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'includes/db_connect.php';

$is_logged_in = isset($_SESSION['user_id']);
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

try {
    $stmt = $db->prepare("SELECT id, name FROM categories ORDER BY name");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = 'Error fetching categories: ' . $e->getMessage();
}

$category_id = isset($_GET['category']) ? intval($_GET['category']) : 0;

$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'created_at';
$sort_order = isset($_GET['order']) ? $_GET['order'] : 'DESC'; 
$valid_sorts = ['title', 'created_at', 'updated_at'];
$valid_orders = ['ASC', 'DESC'];

if (!in_array($sort_by, $valid_sorts)) {
    $sort_by = 'created_at';
}

if (!in_array($sort_order, $valid_orders)) {
    $sort_order = 'DESC';
}


try {
    $sql = "SELECT p.id, p.title, p.content, p.image_path, p.created_at, p.updated_at 
            FROM pages p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE (:category_id = 0 OR p.category_id = :category_id)
            ORDER BY $sort_by $sort_order";
    $stmt = $db->prepare($sql);
    $stmt->execute([':category_id' => $category_id]);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = 'Error fetching posts: ' . $e->getMessage();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
    $comment = trim($_POST['comment']);
    $name = isset($_POST['name']) ? trim($_POST['name']) : 'Anonymous'; 

    if ($post_id > 0 && !empty($comment)) {
        try {
            $stmt = $db->prepare("INSERT INTO comments (post_id, comment, created_at, is_visible) VALUES (:post_id, :comment, NOW(), 1)");
            $stmt->execute([':post_id' => $post_id, ':comment' => $comment]);

            $_SESSION['temp_comment_data'] = [
                'post_id' => $post_id,
                'comment' => $comment,
                'name' => $name
            ];

            $success_message = 'Comment added successfully!';
        } catch (PDOException $e) {
            $error_message = 'Error adding comment: ' . $e->getMessage();
        }
    } else {
        $error_message = 'Please enter a comment and select a post.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_comment'])) {
    $comment_id = isset($_POST['comment_id']) ? intval($_POST['comment_id']) : 0;

    if ($comment_id > 0 && $is_admin) {
        try {
            $stmt = $db->prepare("DELETE FROM comments WHERE id = :comment_id");
            $stmt->execute([':comment_id' => $comment_id]);
            $success_message = 'Comment deleted successfully!';
        } catch (PDOException $e) {
            $error_message = 'Error deleting comment: ' . $e->getMessage();
        }
    } else {
        $error_message = 'Invalid comment ID or insufficient permissions.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_comment'])) {
    $comment_id = isset($_POST['comment_id']) ? intval($_POST['comment_id']) : 0;
    $new_comment = trim($_POST['new_comment']);

    if ($comment_id > 0 && !empty($new_comment)) {
        try {
            $stmt = $db->prepare("UPDATE comments SET comment = :new_comment WHERE id = :comment_id");
            $stmt->execute([':new_comment' => $new_comment, ':comment_id' => $comment_id]);
            $success_message = 'Comment updated successfully!';
        } catch (PDOException $e) {
            $error_message = 'Error updating comment: ' . $e->getMessage();
        }
    } else {
        $error_message = 'Invalid comment ID or empty comment.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['disemvowel_comment'])) {
    $comment_id = isset($_POST['comment_id']) ? intval($_POST['comment_id']) : 0;

    if ($comment_id > 0 && $is_admin) {
        try {

            function disemvowel($text) {
                return preg_replace('/[aeiouAEIOU]/', '', $text);
            }

            $stmt = $db->prepare("SELECT comment FROM comments WHERE id = :comment_id");
            $stmt->execute([':comment_id' => $comment_id]);
            $comment = $stmt->fetchColumn();

            if ($comment !== false) {

                $disemvoweled_comment = disemvowel($comment);
                $stmt = $db->prepare("UPDATE comments SET comment = :comment WHERE id = :comment_id");
                $stmt->execute([':comment' => $disemvoweled_comment, ':comment_id' => $comment_id]);
                $success_message = 'Comment disemvoweled successfully!';
            } else {
                $error_message = 'Comment not found.';
            }
        } catch (PDOException $e) {
            $error_message = 'Error disemvoweling comment: ' . $e->getMessage();
        }
    } else {
        $error_message = 'Invalid comment ID or insufficient permissions.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minecraft Block Database</title>
    <link rel="stylesheet" href="styles.css"> 
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main>
        <section class="intro">
            <h1>Minecraft Block Database</h1>

            <section class="categories-list">
                <h2>Categories</h2>
                <?php if (isset($error_message)): ?>
                    <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
                <?php endif; ?>

                <ul>
                    <?php foreach ($categories as $category): ?>
                        <li>
                            <a href="index.php?category=<?php echo htmlspecialchars($category['id']); ?>">
                                <?php echo htmlspecialchars($category['name']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>

            <form action="search.php" method="GET" class="search-form">
                <input type="text" name="query" placeholder="Search for [pages]..." required>
                <button type="submit">Search</button>
            </form>
        </section>

        <section class="posts">
            <?php if (isset($error_message)): ?>
                <p class="error"><?php echo htmlspecialchars($error_message); ?></p>
            <?php endif; ?>

            <?php if (isset($success_message)): ?>
                <p class="success"><?php echo htmlspecialchars($success_message); ?></p>
            <?php endif; ?>

            <h2>All Posts</h2>

            <?php if ($is_logged_in): ?>
                <section class="create-post">
                    <a href="pages/create_page.php" class="button">Create New Post</a>
                </section>
            <?php endif; ?>

            <form method="get" action="">
    <label for="category">Category:</label>
    <select id="category" name="category" onchange="this.form.submit()">
        <option value="0">All Categories</option>
        <?php foreach ($categories as $category): ?>
            <option value="<?php echo htmlspecialchars($category['id']); ?>" 
                <?php echo $category_id === $category['id'] ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($category['name']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="sort">Sort by:</label>
    <select id="sort" name="sort" onchange="this.form.submit()">
        <option value="created_at" <?php echo $sort_by === 'created_at' ? 'selected' : ''; ?>>Created At</option>
        <option value="updated_at" <?php echo $sort_by === 'updated_at' ? 'selected' : ''; ?>>Updated At</option>
        <option value="title" <?php echo $sort_by === 'title' ? 'selected' : ''; ?>>Title</option>
    </select>

    <label for="order">Order:</label>
    <select id="order" name="order" onchange="this.form.submit()">
        <option value="ASC" <?php echo $sort_order === 'ASC' ? 'selected' : ''; ?>>Ascending</option>
        <option value="DESC" <?php echo $sort_order === 'DESC' ? 'selected' : ''; ?>>Descending</option>
    </select>
</form>

            <?php if ($posts): ?>
                <?php foreach ($posts as $post): ?>
                    <article class="post">
                        <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                        <p><?php echo htmlspecialchars($post['content']); ?></p>
                        <?php if (!empty($post['image_path'])): ?>
                            <img src="<?php echo htmlspecialchars($post['image_path']); ?>" alt="Image for <?php echo htmlspecialchars($post['title']); ?>" style="max-width: 100%; height: auto;">
                        <?php endif; ?>
                        <p class="post-date">Created on: <?php echo htmlspecialchars($post['created_at']); ?></p>
                        <p class="post-date">Updated on: <?php echo htmlspecialchars($post['updated_at']); ?></p>

                        <?php if ($is_logged_in): ?>
                            <div class="post-actions">
                                <a href="pages/edit_page.php?id=<?php echo $post['id']; ?>" class="button">Edit</a>
                                <?php if ($is_admin): ?>
                                    <a href="pages/delete_page.php?id=<?php echo $post['id']; ?>" class="button" onclick="return confirm('Are you sure you want to delete this post?');">Delete</a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <form action="index.php" method="post" class="comment-form">
                            <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($post['id']); ?>">
                            <textarea name="comment" rows="4" placeholder="Leave a comment..." required></textarea>
                            <?php if (!isset($_SESSION['user_id'])): ?>
                                <input type="text" name="name" placeholder="Your name (optional)">
                            <?php endif; ?>
                            <button type="submit">Submit Comment</button>
                        </form>

                        <?php
                        try {
                            $stmt = $db->prepare("SELECT id, comment, created_at FROM comments WHERE post_id = :post_id AND is_visible = 1 ORDER BY created_at DESC");
                            $stmt->execute([':post_id' => $post['id']]);
                            $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        } catch (PDOException $e) {
                            $error_message = 'Error fetching comments: ' . $e->getMessage();
                        }
                        ?>

                        <?php if ($comments): ?>
                            <div class="comments">
                                <h4>Comments:</h4>
                                <ul>
                                    <?php foreach ($comments as $comment): ?>
                                        <li>
                                            <p><?php echo htmlspecialchars($comment['comment']); ?></p>
                                            <p class="comment-date">Commented on: <?php echo htmlspecialchars($comment['created_at']); ?></p>
                                            <?php if ($is_admin): ?>
                                                <form action="index.php" method="post" style="display:inline;">
                                                    <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                                    <input type="hidden" name="delete_comment" value="1">
                                                    <button type="submit" onclick="return confirm('Are you sure you want to delete this comment?');">Delete</button>
                                                </form>
                                                <form action="index.php" method="post" style="display:inline;">
                                                    <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                                    <input type="text" name="new_comment" placeholder="Edit comment">
                                                    <input type="hidden" name="edit_comment" value="1">
                                                    <button type="submit">Edit</button>
                                                </form>
                                                <form action="index.php" method="post" style="display:inline;">
                                                    <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                                    <input type="hidden" name="disemvowel_comment" value="1">
                                                    <button type="submit">Disemvowel</button>
                                                </form>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No posts found.</p>
            <?php endif; ?>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
