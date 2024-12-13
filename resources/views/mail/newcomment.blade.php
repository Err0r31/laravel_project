<div class="card" style="width: 18rem;">
    <div class="card-body">
      <h5 class="card-title">Добавлен новый комментарий для статьи</h5>
      <h6 class="card-subtitle mb-2 text-body-secondary">{{$article->name}}</h6>
      <p class="card-text">{{$comment->desc}}</p>
      <p>Для модерации комментария перейдите по ссылке</p><a href="http://127.0.0.1:3000/comment/index" class="card-link">New comment</a>
    </div>
</div>
