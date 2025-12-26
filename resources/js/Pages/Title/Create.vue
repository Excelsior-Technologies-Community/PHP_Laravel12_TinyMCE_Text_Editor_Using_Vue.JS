<template>
    <div class="card shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">Create Title</h4>
        </div>

        <div class="card-body">
            <form method="POST" action="/store">
                <input type="hidden" name="_token" :value="csrf">

                <!-- Title -->
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input
                        type="text"
                        name="title"
                        class="form-control"
                        placeholder="Enter title"
                        required
                    >
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea id="editor"></textarea>
                    <input type="hidden" name="description" :value="description">
                </div>

                <!-- Actions -->
                <div class="d-flex gap-2">
                    <button class="btn btn-success">
                        Save
                    </button>
                    <a href="/" class="btn btn-secondary">
                        Back
                    </a>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            description: '',
            csrf: document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute('content')
        }
    },
    mounted() {
        tinymce.init({
            selector: '#editor',
            height: 280,
            menubar: false,
            plugins: 'lists link code',
            toolbar: 'undo redo | bold italic | bullist numlist | code',
            setup: (editor) => {
                editor.on('Change KeyUp', () => {
                    this.description = editor.getContent()
                })
            }
        })
    }
}
</script>
