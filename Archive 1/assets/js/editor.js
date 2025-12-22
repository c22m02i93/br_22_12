(function () {
  'use strict';

  var initEditor = function (textarea) {
    if (!window.ClassicEditor || textarea.dataset.editorInitialized) {
      return;
    }

    window.ClassicEditor
      .create(textarea, {
        toolbar: {
          items: [
            'undo', 'redo', '|',
            'heading', '|',
            'bold', 'italic', 'underline', 'link', '|',
            'bulletedList', 'numberedList', 'blockQuote', '|',
            'insertTable', 'imageUpload', 'mediaEmbed'
          ]
        },
        language: 'ru'
      })
      .then(function (editor) {
        textarea.dataset.editorInitialized = '1';
        textarea.addEventListener('formdata', function () {
          textarea.value = editor.getData();
        });
      })
      .catch(function (error) {
        console.error('CKEditor initialisation error', error);
      });
  };

  var scanEditors = function () {
    var areas = document.querySelectorAll('textarea[data-editor="rich"]');
    areas.forEach(initEditor);
  };

  if (document.readyState !== 'loading') {
    scanEditors();
  } else {
    document.addEventListener('DOMContentLoaded', scanEditors);
  }
})();