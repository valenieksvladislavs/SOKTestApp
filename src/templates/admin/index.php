<?php include(__DIR__ . "/header.html"); ?>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Site Sections</h1>
    <a class="btn btn-primary" href="/admin/add-section">Add Section</a>
  </div>

  <!-- Message if there are no sections -->
  <div id="no-sections-message"
    class="alert alert-info text-center my-4 <?= !empty($sectionsTree) ? 'd-none' : '' ?>"
    role="alert">
    <strong>No sections found.</strong> You can add a new section by clicking the button above.
  </div>

  <?php
  // Function for recursive sections output
  function renderSections(array $sections): void
  {
    if (empty($sections)) {
      return;
    }
    echo '<ul class="list-group nested-list my-2">';
    foreach ($sections as $section) {
      echo '<li class="list-group-item" data-section-id="' . $section['id'] . '">';
      echo '<div class="d-flex flex-row justify-content-between align-items-center py-2">';
      echo '<span>' . htmlspecialchars($section['title']) . '</span>';

      echo '<div>';
      echo "<a href=\"/admin/edit-section/{$section['id']}\" class=\"btn btn-sm btn-secondary me-2\">Edit</a>";
      echo '<button class="btn btn-sm btn-danger btn-delete-section" data-id="' . $section['id'] . '">Delete</button>';
      echo '</div>';
      echo '</div>';

      if (isset($section['children']) && !empty($section['children'])) {
        renderSections($section['children']);
      }
      echo '</li>';
    }
    echo '</ul>';
  }
  ?>

  <?php renderSections($sectionsTree); ?>
</div>

<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmDeleteLabel">Confirm deletion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Deleting this section will also remove all its children. Are you sure?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="errorModalLabel">Error</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="errorModalBody">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>

<script src="/public/js/delete-section.min.js"></script>
