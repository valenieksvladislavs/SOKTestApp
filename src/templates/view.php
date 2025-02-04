<?= $header ?>

<div class="container py-4">
  <h1 class="h3 mb-3"><?= htmlspecialchars($targetSection['title']) ?></h1>

  <nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb">
      <?php foreach ($breadcrumbs as $crumb): ?>
        <li class="breadcrumb-item">
          <a href="<?= $crumb['url'] ?>">
            <?= htmlspecialchars($crumb['title']) ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ol>
  </nav>

  <?php if (!empty($childSections)): ?>
    <div class="mb-4">
      <ul class="list-group">
        <?php foreach ($childSections as $child): ?>
          <li class="list-group-item">
            <a href="/view/<?= $targetSection['slug'] ?>/<?= $child['slug'] ?>">
              <?= htmlspecialchars($child['title']) ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if ($targetSection['text']): ?>
    <div class="card">
      <div class="card-body">
        <?= $targetSection['text'] ?>
      </div>
    </div>
  <?php endif ?>
</div>
