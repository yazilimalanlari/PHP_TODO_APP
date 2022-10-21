<main>
    <div class="container">
        <div class="tasks primary-content">
            <div class="header">
                <h1>My Tasks</h1>
                <button class="blue">Add Task</button>
            </div>
            <ul>
                <?php foreach ($tasks as $task): ?>
                <li data-id="<?=$task->id?>" <?=$task->completed ? ' class="completed"' : ''?>>
                    <span class="task-content"><?=$task->content?></span>
                    <div class="actions">
                        <span class="edit">
                            <img src="/images/icons/edit.svg" alt="Edit icon">
                        </span>
                        <span class="delete">
                            <img src="/images/icons/delete.svg" alt="Delete icon">
                        </span>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
            <div class="pagination">
                <?php for($i = 1; $i <= $totalPage; $i++): ?>
                    <a href="/?page=<?=$i?>" <?=($_GET['page'] ?? 1) == $i ? ' class="active"' : ''?>><?=$i?></a>
                <?php endfor; ?>
            </div>
        </div>
    </div>
    <div class="modal">
        <div class="inner">
            <h1 class="title"></h1>
            <p class="content"></p>
            <div class="actions">
                <button class="green" id="yes">Yes</button>
                <button class="red" id="cancel">Cancel</button>
            </div>
        </div>
    </div>
</main>