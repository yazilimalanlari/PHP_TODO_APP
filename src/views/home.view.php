<main>
    <div class="container">
        <h1>Todo App</h1>
        <ul>
            <?php foreach ($tasks as $task): ?>
            <li><?=$task->content?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</main>