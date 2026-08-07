<template>
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Title Management</h4>
            <a href="/create" class="btn btn-primary btn-sm">+ Create New</a>
        </div>

        <div class="card-body">
            <form method="GET" action="/" class="row g-2 mb-3">
                <div class="col-md-4">
                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Search titles..."
                        v-model="search"
                    >
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select" v-model="status">
                        <option value="">All Status</option>
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">Filter</button>
                </div>
                <div class="col-md-2">
                    <a href="/" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>

            <form id="bulkForm" method="POST" action="/bulk-delete" @submit.prevent="bulkDelete">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                        <input
                            type="checkbox"
                            id="selectAll"
                            @change="toggleSelectAll"
                        >
                        <label for="selectAll" class="ms-2">Select All</label>
                    </div>
                    <button
                        type="submit"
                        class="btn btn-danger btn-sm"
                        :disabled="selectedIds.length === 0"
                        onclick="return confirm('Delete selected?')"
                    >
                        Bulk Delete ({{ selectedIds.length }})
                    </button>
                </div>

                <input type="hidden" name="_token" :value="csrf">

                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="4%">
                                    <input
                                        type="checkbox"
                                        :checked="allSelected"
                                        @change="toggleSelectAll"
                                    >
                                </th>
                                <th width="5%">
                                    <a href="?sort=id&direction={{ sortDir('id') }}" class="text-decoration-none text-dark">
                                        ID {{ sortIcon('id') }}
                                    </a>
                                </th>
                                <th width="20%">
                                    <a href="?sort=title&direction={{ sortDir('title') }}" class="text-decoration-none text-dark">
                                        Title {{ sortIcon('title') }}
                                    </a>
                                </th>
                                <th>Description</th>
                                <th width="8%">Status</th>
                                <th width="15%">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="item in titles.data" :key="item.id">
                                <td>
                                    <input
                                        type="checkbox"
                                        :value="item.id"
                                        v-model="selectedIds"
                                    >
                                </td>
                                <td>{{ item.id }}</td>
                                <td class="fw-semibold">{{ item.title }}</td>
                                <td v-html="item.description"></td>
                                <td>
                                    <span
                                        class="badge"
                                        :class="{
                                            'bg-success': item.status === 'published',
                                            'bg-warning': item.status === 'draft',
                                            'bg-secondary': item.status === 'archived'
                                        }"
                                    >
                                        {{ item.status }}
                                    </span>
                                </td>
                                <td>
                                    <a :href="`/edit/${item.id}`" class="btn btn-warning btn-sm me-1">Edit</a>
                                    <a :href="`/preview/${item.id}`" class="btn btn-info btn-sm me-1">Preview</a>
                                    <a
                                        :href="`/delete/${item.id}`"
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure?')"
                                    >
                                        Delete
                                    </a>
                                </td>
                            </tr>

                            <tr v-if="titles.data.length === 0">
                                <td colspan="6" class="text-center text-muted py-4">No records found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </form>

            <nav v-if="titles.last_page > 1" class="mt-3">
                <ul class="pagination justify-content-center">
                    <li class="page-item" :class="{ disabled: titles.current_page === 1 }">
                        <a class="page-link" :href="`/?page=${titles.current_page - 1}`">Previous</a>
                    </li>
                    <li
                        v-for="page in pageNumbers"
                        :key="page"
                        class="page-item"
                        :class="{ active: page === titles.current_page }"
                    >
                        <a class="page-link" :href="`/?page=${page}`">{{ page }}</a>
                    </li>
                    <li class="page-item" :class="{ disabled: titles.current_page === titles.last_page }">
                        <a class="page-link" :href="`/?page=${titles.current_page + 1}`">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        const el = document.getElementById('app')
        const urlParams = new URLSearchParams(window.location.search)
        return {
            titles: JSON.parse(el.dataset.titles || '{"data":[]}'),
            selectedIds: [],
            csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            search: urlParams.get('search') || '',
            status: urlParams.get('status') || ''
        }
    },
    computed: {
        allSelected() {
            return this.titles.data.length > 0 && this.selectedIds.length === this.titles.data.length
        },
        pageNumbers() {
            const pages = []
            const start = Math.max(1, this.titles.current_page - 2)
            const end = Math.min(this.titles.last_page, start + 4)
            for (let i = start; i <= end; i++) pages.push(i)
            return pages
        }
    },
    methods: {
        toggleSelectAll() {
            if (this.allSelected) {
                this.selectedIds = []
            } else {
                this.selectedIds = this.titles.data.map(item => item.id)
            }
        },
        bulkDelete() {
            if (!confirm('Delete selected items?')) return
            const form = document.getElementById('bulkForm')
            const formData = new FormData()
            formData.append('_token', this.csrf)
            this.selectedIds.forEach(id => formData.append('ids[]', id))

            fetch('/bulk-delete', {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(() => window.location.reload())
        },
        sortDir(field) {
            const params = new URLSearchParams(window.location.search)
            const currentSort = params.get('sort')
            const currentDir = params.get('direction')
            if (currentSort === field) {
                return currentDir === 'asc' ? 'desc' : 'asc'
            }
            return 'asc'
        },
        sortIcon(field) {
            const params = new URLSearchParams(window.location.search)
            if (params.get('sort') === field) {
                return params.get('direction') === 'asc' ? '▲' : '▼'
            }
            return ''
        }
    }
}
</script>
