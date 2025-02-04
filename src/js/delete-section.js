$(document).ready(function() {
  let sectionIdToDelete = null;
  let $liToRemove = null;

  $(document).on('click', '.btn-delete-section', function() {
    sectionIdToDelete = $(this).data('id');
    $liToRemove = $(this).closest('li[data-section-id]');
    $('#confirmDeleteModal').modal('show');
  });

  $('#confirmDeleteBtn').on('click', function() {
    if (!sectionIdToDelete) {
      return;
    }
    $.ajax({
      url: '/admin/delete-section/' + sectionIdToDelete,
      method: 'POST',
      dataType: 'json',
      success: function(response) {
        if (response.success) {
          if ($liToRemove) {
            $liToRemove.remove();
          }

          if ($('li[data-section-id]').length === 0) {
            $('#no-sections-message').removeClass('d-none');
          }
        } else {
          showErrorModal(response.error || 'Unable to delete the section');
        }
      },
      error: function() {
        showErrorModal('Server error or network issue occurred');
      }
    });
    $('#confirmDeleteModal').modal('hide');
  });

  function showErrorModal(message) {
    $('#errorModalBody').text(message);
    $('#errorModal').modal('show');
  }
});
