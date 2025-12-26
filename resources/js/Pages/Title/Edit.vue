<template>
    <div class="card shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">Edit Title</h4>
        </div>

        <div class="card-body">
            <form :action="`/update/${item.id}`" method="POST">
                <input type="hidden" name="_token" :value="csrf">

                <!-- Title -->
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input
                        type="text"
                        name="title"
                        v-model="title"
                        class="form-control"
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
                    <button class="btn btn-primary">
                        Update
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
        const el = document.getElementById('app')
        const item = JSON.parse(el.dataset.item)

        return {
            item,
            title: item.title,
            description: item.description,
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
                editor.on('init', () => {
                    editor.setContent(this.description)
                })
                editor.on('Change KeyUp', () => {
                    this.description = editor.getContent()
                })
            }
        })
    }
}
</script>
