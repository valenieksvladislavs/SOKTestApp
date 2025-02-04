<?php include(__DIR__ . "/header.html") ?>

<div class="container py-4">
  <h1 class="h3 mb-4"><?= $title ?></h1>

  <div class="card">
    <div class="card-body">

      <div id="systemError" class="alert alert-danger d-none"></div>

      <form id="addSectionForm" method="post">
        <div class="mb-3">
          <label for="sectionTitle" class="form-label">Section Title</label>
          <input type="text" class="form-control" id="sectionTitle" name="title" placeholder="Enter section title" value="<?= $existingSection['title'] ?? '' ?>" required>
        </div>

        <div class="mb-3">
          <label for="sectionSlug" class="form-label">Section Slug</label>
          <input type="text" class="form-control" id="sectionSlug" name="slug" placeholder="Enter section slug" value="<?= $existingSection['slug'] ?? '' ?>" required>
          <div class="form-text">This will be used in the URL.</div>
        </div>

        <div class="mb-3">
          <label for="sectionDescription" class="form-label">Section Content</label>
          <textarea name="text" id="sectionContent"><?=$existingSection['text'] ?? ''?></textarea>
        </div>

        <div class="mb-3">
          <label for="parentSection" class="form-label">Parent Section (optional)</label>
          <select class="form-select" id="parentSection" name="parent_id">
            <option value="" <?= !isset($existingSection['parent_id']) || empty($existingSection['parent_id']) ? 'selected="selected"' : '' ?>>
              None
            </option>
            <?php foreach ($sectionOptions as $section): ?>
              <option value="<?= $section['id']?>" <?= isset($existingSection['parent_id']) && $existingSection['parent_id'] === $section['id'] ? 'selected="selected"' : '' ?>>
                <?= $section['title'] ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="d-flex justify-content-end">
          <button type="submit" class="btn btn-primary me-2">Save Section</button>
          <a href="/admin" class="btn btn-secondary">Cancel</a>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="successModalLabel">Success</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?= $successMessage ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="successModalBtn" data-bs-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>

<script src="/public/tinymce/tinymce.min.js"></script>

<script src="/public/js/edit-section.min.js"></script>
