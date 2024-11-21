<?php

namespace app\controllers;

use app\models\Post;

class PostController
{
    // Validate input data for creating/updating posts
    public function validatePost($inputData) {
        $errors = [];
        $title = $inputData['title'];
        $content = $inputData['content'];

        if ($title) {
            $title = htmlspecialchars($title, ENT_QUOTES|ENT_HTML5, 'UTF-8', true);
            if (strlen($title) < 3) {
                $errors['titleShort'] = 'Post title is too short';
            }
        } else {
            $errors['requiredTitle'] = 'Title is required';
        }

        if ($content) {
            $content = htmlspecialchars($content, ENT_QUOTES|ENT_HTML5, 'UTF-8', true);
            if (strlen($content) < 5) {
                $errors['contentShort'] = 'Post content is too short';
            }
        } else {
            $errors['requiredContent'] = 'Content is required';
        }

        if (count($errors)) {
            http_response_code(400);
            echo json_encode($errors);
            exit();
        }

        return [
            'title' => $title,
            'content' => $content,
        ];
    }

    // Get all posts
    public function getAllPosts() {
        $postModel = new Post();
        header("Content-Type: application/json");
        $posts = $postModel->getAllPosts();
        echo json_encode($posts);
        exit();
    }

    // Get post by ID
    public function getPostByID($id) {
        $postModel = new Post();
        header("Content-Type: application/json");
        $post = $postModel->getPostById($id);
        echo json_encode($post);
        exit();
    }

    // Save a new post
    public function savePost() {
        $inputData = [
            'title' => $_POST['title'] ? $_POST['title'] : false,
            'content' => $_POST['content'] ? $_POST['content'] : false,
        ];
        $postData = $this->validatePost($inputData);

        $post = new Post();
        $post->savePost(
            [
                'title' => $postData['title'],
                'content' => $postData['content'],
            ]
        );

        http_response_code(200);
        echo json_encode([
            'success' => true
        ]);
        exit();
    }

    // Update post
    public function updatePost($id) {
        if (!$id) {
            http_response_code(404);
            exit();
        }

        // No built-in super global for PUT method
        parse_str(file_get_contents('php://input'), $_PUT);

        $inputData = [
            'title' => $_PUT['title'] ? $_PUT['title'] : false,
            'content' => $_PUT['content'] ? $_PUT['content'] : false,
        ];
        $postData = $this->validatePost($inputData);

        $post = new Post();
        $post->updatePost(
            [
                'id' => $id,
                'title' => $postData['title'],
                'content' => $postData['content'],
            ]
        );

        http_response_code(200);
        echo json_encode([
            'success' => true
        ]);
        exit();
    }

    // Delete post
    public function deletePost($id) {
        if (!$id) {
            http_response_code(404);
            exit();
        }

        $post = new Post();
        $post->deletePost(
            [
                'id' => $id,
            ]
        );

        http_response_code(200);
        echo json_encode([
            'success' => true
        ]);
        exit();
    }

    // Post views
    public function postsView() {
        include '../public/assets/views/post/posts-view.html';
        exit();
    }

    public function postsAddView() {
        include '../public/assets/views/post/posts-add.html';
        exit();
    }

    public function postsDeleteView() {
        include '../public/assets/views/post/posts-delete.html';
        exit();
    }

    public function postsUpdateView() {
        include '../public/assets/views/post/posts-update.html';
        exit();
    }
}
