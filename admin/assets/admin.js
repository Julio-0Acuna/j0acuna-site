const projectsContainer = document.querySelector('[data-projects]');
const template = document.querySelector('#project-template');
const addButton = document.querySelector('[data-add-project]');

function refreshProjectNames() {
  projectsContainer?.querySelectorAll('[data-project]').forEach((project, index) => {
    project.querySelectorAll('[data-name]').forEach((field) => {
      field.name = `projects[${index}][${field.dataset.name}]`;
    });
  });
}

function bindProjectPreview(project) {
  const imageInput = project.querySelector('[name$="[image_url]"], [data-name="image_url"]');
  const titleInput = project.querySelector('[name$="[title]"], [data-name="title"]');
  const titleLabel = project.querySelector('.project-editor-header strong');
  let preview = project.querySelector('.project-preview');

  titleInput?.addEventListener('input', () => {
    titleLabel.textContent = titleInput.value.trim() || 'Proyecto nuevo';
  });

  imageInput?.addEventListener('input', () => {
    const value = imageInput.value.trim();

    if (!value) {
      preview?.remove();
      preview = null;
      return;
    }

    if (!preview) {
      preview = document.createElement('img');
      preview.className = 'project-preview';
      preview.alt = 'Preview del proyecto';
      project.querySelector('.project-editor-header')?.after(preview);
    }

    preview.src = value.startsWith('http') ? value : `../${value}`;
  });
}

addButton?.addEventListener('click', () => {
  if (!template || !projectsContainer) return;

  const node = template.content.firstElementChild.cloneNode(true);
  projectsContainer.appendChild(node);
  refreshProjectNames();
  bindProjectPreview(node);
  node.querySelector('input[data-name="title"]')?.focus();
});

projectsContainer?.addEventListener('click', (event) => {
  const button = event.target.closest('[data-remove-project]');
  if (!button) return;

  if (!confirm('Seguro que quieres eliminar este proyecto del portafolio?')) return;

  button.closest('[data-project]')?.remove();
  refreshProjectNames();
});

projectsContainer?.querySelectorAll('[data-project]').forEach(bindProjectPreview);
