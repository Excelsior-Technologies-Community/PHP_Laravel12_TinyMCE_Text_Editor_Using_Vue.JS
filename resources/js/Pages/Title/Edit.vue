<template>
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Edit Title</h4>
                    <div>
                        <button type="button" class="btn btn-outline-secondary btn-sm me-1" @click="showPreview = true">Preview</button>
                        <a href="/" class="btn btn-secondary btn-sm">Back</a>
                    </div>
                </div>

                <div class="card-body">
                    <form ref="editForm" :action="`/update/${item.id}`" method="POST" @submit.prevent="submitForm">
                        <input type="hidden" name="_token" :value="csrf">
                        <input type="hidden" name="_method" value="PUT">

                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input
                                type="text"
                                name="title"
                                v-model="title"
                                class="form-control"
                                required
                            >
                            <div class="form-text">
                                Words: {{ wordCount }} | Characters: {{ charCount }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" v-model="status">
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea id="editor"></textarea>
                            <input type="hidden" name="description" :value="description">
                            <div class="form-text d-flex justify-content-between">
                                <span>Auto-saved: {{ lastSaved }}</span>
                                <span v-if="saving" class="text-primary">Saving...</span>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Comments / Notes</h5>
                </div>
                <div class="card-body">
                    <div v-if="comments.length === 0" class="text-muted">No comments yet.</div>
                    <div v-for="comment in comments" :key="comment.id" class="border rounded p-2 mb-2">
                        <div class="d-flex justify-content-between">
                            <strong>{{ comment.user_name || 'Anonymous' }}</strong>
                            <small class="text-muted">{{ formatDate(comment.created_at) }}</small>
                        </div>
                        <p class="mb-0 mt-1">{{ comment.body }}</p>
                    </div>
                </div>
                <div class="card-footer">
                    <form @submit.prevent="addComment">
                        <div class="input-group">
                            <input
                                type="text"
                                class="form-control"
                                placeholder="Add a comment..."
                                v-model="newComment"
                                required
                            >
                            <button type="submit" class="btn btn-primary">Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Revisions History</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Changed By</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="rev in revisions" :key="rev.id">
                                <td>{{ formatDate(rev.created_at) }}</td>
                                <td>{{ rev.changed_by }}</td>
                                <td>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        @click="restoreRevision(rev)"
                                    >
                                        Restore
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="revisions.length === 0">
                                <td colspan="3" class="text-center text-muted">No revisions yet</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div v-if="showPreview" class="modal fade show d-block" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Preview</h5>
                    <button type="button" class="btn-close" @click="showPreview = false"></button>
                </div>
                <div class="modal-body">
                    <h4>{{ title }}</h4>
                    <span
                        class="badge mb-3"
                        :class="{
                            'bg-success': status === 'published',
                            'bg-warning': status === 'draft',
                            'bg-secondary': status === 'archived'
                        }"
                    >
                        {{ status }}
                    </span>
                    <div v-html="description"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="showPreview = false">Close</button>
                </div>
            </div>
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
            status: item.status || 'draft',
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            revisions: JSON.parse(el.dataset.revisions || '[]'),
            comments: JSON.parse(el.dataset.comments || '[]'),
            newComment: '',
            saving: false,
            lastSaved: 'Never',
            showPreview: false,
            autosaveTimer: null
        }
    },
    computed: {
        wordCount() {
            const text = this.title.replace(/<[^>]*>/g, '')
            return text.trim() ? text.trim().split(/\s+/).length : 0
        },
        charCount() {
            return this.title.replace(/<[^>]*>/g, '').length
        }
    },
    mounted() {
        tinymce.init({
            selector: '#editor',
            height: 350,
            menubar: true,
            plugins: 'lists link image table media fullscreen code codesample',
            toolbar: 'undo redo | formatselect | bold italic underline strikethrough | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image table media | codesample code fullscreen',
            image_uploadtab: true,
            automatic_uploads: true,
            images_upload_handler: (blobInfo, success, failure) => {
                const reader = new FileReader()
                reader.onload = () => success(reader.result)
                reader.onerror = failure
                reader.readAsDataURL(blobInfo.blob())
            },
            setup: (editor) => {
                editor.on('init', () => {
                    editor.setContent(this.description)
                })
                editor.on('Change KeyUp', () => {
                    this.description = editor.getContent()
                    this.triggerAutosave()
                })
            }
        })
    },
    methods: {
        submitForm() {
            const form = this.$refs.editForm
            const hiddenInput = document.createElement('input')
            hiddenInput.type = 'hidden'
            hiddenInput.name = 'description'
            hiddenInput.value = this.description
            form.appendChild(hiddenInput)
            form.submit()
        },
        triggerAutosave() {
            this.saving = true
            clearTimeout(this.autosaveTimer)
            this.autosaveTimer = setTimeout(() => {
                this.saveDraft()
            }, 2000)
        },
        saveDraft() {
            const formData = new FormData()
            formData.append('_token', this.csrf)
            formData.append('title', this.title)
            formData.append('description', this.description)
            formData.append('status', this.status)

            fetch(`/autosave/${this.item.id}`, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => {
                if (!res.ok) throw new Error('Autosave failed')
                return res.json()
            })
            .then(() => {
                this.saving = false
                this.lastSaved = new Date().toLocaleTimeString()
            })
            .catch(() => {
                this.saving = false
            })
        },
        addComment() {
            const formData = new FormData()
            formData.append('_token', this.csrf)
            formData.append('body', this.newComment)
            formData.append('user_name', 'User')

            fetch(`/comment/${this.item.id}`, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => {
                if (!res.ok) throw new Error('Comment failed')
                return res.json()
            })
            .then(() => {
                this.newComment = ''
                window.location.reload()
            })
            .catch(() => {
                this.newComment = ''
                window.location.reload()
            })
        },
        restoreRevision(rev) {
            this.title = rev.title
            this.description = rev.description
            this.submitForm()
        },
        formatDate(date) {
            if (!date) return ''
            return new Date(date).toLocaleString()
        }
    }
}
</script>
