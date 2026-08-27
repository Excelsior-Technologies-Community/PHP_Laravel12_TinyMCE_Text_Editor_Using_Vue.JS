<template>

    <div class="title-management">

        <!-- =========================================================
             SUCCESS ALERT
        ========================================================== -->

        <transition name="slide-down">

            <div
                v-if="showSuccess"
                class="success-notification"
            >

                <div class="success-icon">
                    ✓
                </div>

                <div class="success-content">

                    <strong>Success</strong>

                    <span>
                        {{ successMessage }}
                    </span>

                </div>

                <button
                    type="button"
                    class="notification-close"
                    @click="showSuccess = false"
                >
                    ×
                </button>

            </div>

        </transition>


        <!-- =========================================================
             PAGE HEADER
        ========================================================== -->

        <div class="page-header">

            <div class="header-left">

                <div class="page-icon">
                    📝
                </div>

                <div>

                    <h1>
                        Title Management
                    </h1>

                    <p>
                        Manage, organize and track your titles
                    </p>

                </div>

            </div>


            <div class="header-actions">

                <a
                    :href="exportUrl"
                    class="btn-modern btn-export"
                >

                    <span>📥</span>

                    Export CSV

                </a>


                <a
                    href="/create"
                    class="btn-modern btn-create"
                >

                    <span>＋</span>

                    Create New

                </a>

            </div>

        </div>


        <!-- =========================================================
             STAT CARDS
        ========================================================== -->

        <div class="stats-grid">

            <div class="stat-card">

                <div class="stat-icon total-icon">
                    📚
                </div>

                <div class="stat-info">

                    <span class="stat-label">
                        Total Titles
                    </span>

                    <strong>
                        {{ titles.total || 0 }}
                    </strong>

                </div>

            </div>


            <div class="stat-card">

                <div class="stat-icon published-icon">
                    ✓
                </div>

                <div class="stat-info">

                    <span class="stat-label">
                        Published
                    </span>

                    <strong>
                        {{ publishedCount }}
                    </strong>

                </div>

            </div>


            <div class="stat-card">

                <div class="stat-icon draft-icon">
                    ✎
                </div>

                <div class="stat-info">

                    <span class="stat-label">
                        Drafts
                    </span>

                    <strong>
                        {{ draftCount }}
                    </strong>

                </div>

            </div>


            <div class="stat-card">

                <div class="stat-icon favorite-icon">
                    ⭐
                </div>

                <div class="stat-info">

                    <span class="stat-label">
                        Favorites
                    </span>

                    <strong>
                        {{ favoriteCount }}
                    </strong>

                </div>

            </div>

        </div>


        <!-- =========================================================
             MAIN CARD
        ========================================================== -->

        <div class="main-card">


            <!-- =====================================================
                 FILTER HEADER
            ====================================================== -->

            <div class="filter-header">

                <div>

                    <h2>
                        Search & Filters
                    </h2>

                    <p>
                        Find titles quickly using advanced filters
                    </p>

                </div>


                <a
                    href="/"
                    class="reset-filter"
                >

                    ↻ Reset Filters

                </a>

            </div>


            <!-- =====================================================
                 FILTER FORM
            ====================================================== -->

            <form
                method="GET"
                action="/"
                class="filter-form"
            >

                <!-- Search -->

                <div class="filter-group search-group">

                    <label>
                        Search
                    </label>

                    <div class="input-wrapper">

                        <span class="input-icon">
                            🔍
                        </span>

                        <input
                            type="text"
                            name="search"
                            v-model="search"
                            placeholder="Search title or description..."
                        >

                    </div>

                </div>


                <!-- Status -->

                <div class="filter-group">

                    <label>
                        Status
                    </label>

                    <div class="select-wrapper">

                        <select
                            name="status"
                            v-model="status"
                        >

                            <option value="">
                                All Status
                            </option>

                            <option value="draft">
                                Draft
                            </option>

                            <option value="published">
                                Published
                            </option>

                            <option value="archived">
                                Archived
                            </option>

                        </select>

                    </div>

                </div>


                <!-- Favorite -->

                <div class="filter-group">

                    <label>
                        Favorite
                    </label>

                    <div class="select-wrapper">

                        <select
                            name="favorite"
                            v-model="favorite"
                        >

                            <option value="all">
                                All Titles
                            </option>

                            <option value="1">
                                ⭐ Favorites
                            </option>

                            <option value="0">
                                ☆ Non Favorites
                            </option>

                        </select>

                    </div>

                </div>


                <!-- Date From -->

                <div class="filter-group">

                    <label>
                        Created From
                    </label>

                    <input
                        type="date"
                        name="date_from"
                        v-model="dateFrom"
                    >

                </div>


                <!-- Date To -->

                <div class="filter-group">

                    <label>
                        Created To
                    </label>

                    <input
                        type="date"
                        name="date_to"
                        v-model="dateTo"
                    >

                </div>


                <!-- Filter Button -->

                <div class="filter-button-wrapper">

                    <button
                        type="submit"
                        class="apply-filter"
                    >

                        🔍

                        Apply

                    </button>

                </div>

            </form>


            <!-- =====================================================
                 TABLE TOOLBAR
            ====================================================== -->

            <div class="table-toolbar">

                <div class="selection-info">

                    <label class="custom-checkbox">

                        <input
                            type="checkbox"
                            :checked="allSelected"
                            @change="toggleSelectAll"
                        >

                        <span class="checkmark"></span>

                    </label>


                    <span>

                        Select All

                    </span>


                    <span
                        v-if="selectedIds.length"
                        class="selected-count"
                    >

                        {{ selectedIds.length }}
                        selected

                    </span>

                </div>


                <button
                    type="button"
                    class="bulk-delete-btn"
                    :disabled="selectedIds.length === 0"
                    @click="bulkDelete"
                >

                    🗑

                    Delete Selected

                    <span
                        v-if="selectedIds.length"
                    >
                        ({{ selectedIds.length }})
                    </span>

                </button>

            </div>


            <!-- =====================================================
                 TABLE
            ====================================================== -->

            <div class="table-container">

                <table class="modern-table">

                    <thead>

                        <tr>

                            <th class="checkbox-column">

                                <label class="custom-checkbox">

                                    <input
                                        type="checkbox"
                                        :checked="allSelected"
                                        @change="toggleSelectAll"
                                    >

                                    <span class="checkmark"></span>

                                </label>

                            </th>


                            <th>

                                <a
                                    :href="sortUrl('id')"
                                    class="sort-link"
                                >

                                    ID

                                    <span>
                                        {{ sortIcon('id') }}
                                    </span>

                                </a>

                            </th>


                            <th>

                                <a
                                    :href="sortUrl('title')"
                                    class="sort-link"
                                >

                                    Title

                                    <span>
                                        {{ sortIcon('title') }}
                                    </span>

                                </a>

                            </th>


                            <th>
                                Description
                            </th>


                            <th>
                                Status
                            </th>


                            <th class="text-center">
                                Favorite
                            </th>


                            <th>
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <!-- =================================================
                             RECORD
                        ================================================== -->

                        <tr
                            v-for="item in titles.data"
                            :key="item.id"
                            class="table-row"
                        >


                            <!-- Checkbox -->

                            <td>

                                <label class="custom-checkbox">

                                    <input
                                        type="checkbox"
                                        :value="item.id"
                                        v-model="selectedIds"
                                    >

                                    <span class="checkmark"></span>

                                </label>

                            </td>


                            <!-- ID -->

                            <td>

                                <span class="id-badge">

                                    #{{ item.id }}

                                </span>

                            </td>


                            <!-- Title -->

                            <td>

                                <div class="title-cell">

                                    <div class="title-avatar">

                                        {{ getInitial(item.title) }}

                                    </div>

                                    <div>

                                        <div class="title-name">

                                            {{ item.title }}

                                        </div>

                                        <div class="title-meta">

                                            Title ID #{{ item.id }}

                                        </div>

                                    </div>

                                </div>

                            </td>


                            <!-- Description -->

                            <td>

                                <div
                                    class="description-cell"
                                    v-html="item.description"
                                ></div>

                            </td>


                            <!-- Status -->

                            <td>

                                <div class="status-wrapper">

                                    <span
                                        class="status-dot"
                                        :class="
                                            'status-' +
                                            item.status
                                        "
                                    ></span>

                                    <select
                                        class="status-select"
                                        :class="
                                            'status-' +
                                            item.status
                                        "
                                        :value="item.status"
                                        @change="
                                            changeStatus(
                                                item,
                                                $event.target.value
                                            )
                                        "
                                    >

                                        <option value="draft">
                                            Draft
                                        </option>

                                        <option value="published">
                                            Published
                                        </option>

                                        <option value="archived">
                                            Archived
                                        </option>

                                    </select>

                                </div>

                            </td>


                            <!-- Favorite -->

                            <td class="text-center">

                                <button
                                    type="button"
                                    class="favorite-button"
                                    :class="{
                                        active:
                                            item.is_favorite
                                    }"
                                    @click="
                                        toggleFavorite(item)
                                    "
                                    :title="
                                        item.is_favorite
                                            ? 'Remove favorite'
                                            : 'Add favorite'
                                    "
                                >

                                    <span
                                        v-if="
                                            item.is_favorite
                                        "
                                    >
                                        ★
                                    </span>

                                    <span
                                        v-else
                                    >
                                        ☆
                                    </span>

                                </button>

                            </td>


                            <!-- Actions -->

                            <td>

                                <div class="action-buttons">


                                    <!-- Edit -->

                                    <a
                                        :href="
                                            `/edit/${item.id}`
                                        "
                                        class="action-btn edit-btn"
                                        title="Edit"
                                    >

                                        ✎

                                        <span>
                                            Edit
                                        </span>

                                    </a>


                                    <!-- Preview -->

                                    <a
                                        :href="
                                            `/preview/${item.id}`
                                        "
                                        class="action-btn preview-btn"
                                        title="Preview"
                                    >

                                        👁

                                        <span>
                                            Preview
                                        </span>

                                    </a>


                                    <!-- Duplicate -->

                                    <a
                                        :href="
                                            `/duplicate/${item.id}`
                                        "
                                        class="action-btn duplicate-btn"
                                        title="Duplicate"
                                        onclick="
                                            return confirm(
                                                'Create a duplicate of this title?'
                                            )
                                        "
                                    >

                                        📋

                                        <span>
                                            Duplicate
                                        </span>

                                    </a>


                                    <!-- Delete -->

                                    <a
                                        :href="
                                            `/delete/${item.id}`
                                        "
                                        class="action-btn delete-btn"
                                        title="Delete"
                                        onclick="
                                            return confirm(
                                                'Are you sure you want to delete this title?'
                                            )
                                        "
                                    >

                                        🗑

                                        <span>
                                            Delete
                                        </span>

                                    </a>

                                </div>

                            </td>

                        </tr>


                        <!-- =================================================
                             EMPTY STATE
                        ================================================== -->

                        <tr
                            v-if="
                                !titles.data ||
                                titles.data.length === 0
                            "
                        >

                            <td
                                colspan="7"
                                class="empty-state"
                            >

                                <div class="empty-icon">
                                    📂
                                </div>

                                <h3>
                                    No titles found
                                </h3>

                                <p>
                                    Try changing your filters or
                                    create a new title.
                                </p>

                                <a
                                    href="/create"
                                    class="btn-create empty-create"
                                >

                                    ＋ Create New Title

                                </a>

                            </td>

                        </tr>

                    </tbody>

                </table>

            </div>


            <!-- =====================================================
                 PAGINATION
            ====================================================== -->

            <div
                v-if="titles.last_page > 1"
                class="pagination-container"
            >

                <div class="pagination-info">

                    Showing

                    <strong>
                        {{ titles.from || 0 }}
                    </strong>

                    to

                    <strong>
                        {{ titles.to || 0 }}
                    </strong>

                    of

                    <strong>
                        {{ titles.total || 0 }}
                    </strong>

                    titles

                </div>


                <nav>

                    <ul class="modern-pagination">


                        <!-- Previous -->

                        <li
                            :class="{
                                disabled:
                                    titles.current_page === 1
                            }"
                        >

                            <a
                                :href="
                                    titles.current_page === 1
                                        ? '#'
                                        : pageUrl(
                                            titles.current_page - 1
                                        )
                                "
                            >
                                ‹
                            </a>

                        </li>


                        <!-- Page Numbers -->

                        <li
                            v-for="page in pageNumbers"
                            :key="page"
                            :class="{
                                active:
                                    page ===
                                    titles.current_page
                            }"
                        >

                            <a
                                :href="
                                    pageUrl(page)
                                "
                            >

                                {{ page }}

                            </a>

                        </li>


                        <!-- Next -->

                        <li
                            :class="{
                                disabled:
                                    titles.current_page ===
                                    titles.last_page
                            }"
                        >

                            <a
                                :href="
                                    titles.current_page ===
                                    titles.last_page
                                        ? '#'
                                        : pageUrl(
                                            titles.current_page + 1
                                        )
                                "
                            >

                                ›

                            </a>

                        </li>

                    </ul>

                </nav>

            </div>

        </div>

    </div>

</template>


<script>

export default {

    data() {

        const el =
            document.getElementById('app')

        const urlParams =
            new URLSearchParams(
                window.location.search
            )

        return {

            titles:
                JSON.parse(
                    el.dataset.titles ||
                    '{"data":[]}'
                ),

            selectedIds: [],

            successMessage: '',

            showSuccess: false,

            csrf:
                document
                    .querySelector(
                        'meta[name="csrf-token"]'
                    )
                    .getAttribute(
                        'content'
                    ),

            search:
                urlParams.get(
                    'search'
                ) || '',

            status:
                urlParams.get(
                    'status'
                ) || '',

            favorite:
                urlParams.get(
                    'favorite'
                ) || 'all',

            dateFrom:
                urlParams.get(
                    'date_from'
                ) || '',

            dateTo:
                urlParams.get(
                    'date_to'
                ) || ''

        }

    },


    mounted() {

        const success =
            document
                .getElementById('app')
                ?.dataset
                ?.success

        if (success) {

            this.showMessage(
                success
            )

        }

    },


    computed: {

        /* =========================================================
           SELECT ALL
        ========================================================== */

        allSelected() {

            return (
                this.titles.data &&
                this.titles.data.length > 0 &&
                this.selectedIds.length ===
                    this.titles.data.length
            )

        },


        /* =========================================================
           PUBLISHED COUNT
        ========================================================== */

        publishedCount() {

            return (
                this.titles.data || []
            ).filter(
                item =>
                    item.status ===
                    'published'
            ).length

        },


        /* =========================================================
           DRAFT COUNT
        ========================================================== */

        draftCount() {

            return (
                this.titles.data || []
            ).filter(
                item =>
                    item.status ===
                    'draft'
            ).length

        },


        /* =========================================================
           FAVORITE COUNT
        ========================================================== */

        favoriteCount() {

            return (
                this.titles.data || []
            ).filter(
                item =>
                    item.is_favorite
            ).length

        },


        /* =========================================================
           PAGE NUMBERS
        ========================================================== */

        pageNumbers() {

            const pages = []

            let start =
                Math.max(
                    1,
                    this.titles.current_page - 2
                )

            let end =
                Math.min(
                    this.titles.last_page,
                    start + 4
                )

            if (
                end - start < 4
            ) {

                start =
                    Math.max(
                        1,
                        end - 4
                    )

            }

            for (
                let i = start;
                i <= end;
                i++
            ) {

                pages.push(i)

            }

            return pages

        },


        /* =========================================================
           EXPORT URL
        ========================================================== */

        exportUrl() {

            const params =
                new URLSearchParams()

            if (this.search) {

                params.set(
                    'search',
                    this.search
                )

            }

            if (this.status) {

                params.set(
                    'status',
                    this.status
                )

            }

            if (
                this.favorite &&
                this.favorite !== 'all'
            ) {

                params.set(
                    'favorite',
                    this.favorite
                )

            }

            if (this.dateFrom) {

                params.set(
                    'date_from',
                    this.dateFrom
                )

            }

            if (this.dateTo) {

                params.set(
                    'date_to',
                    this.dateTo
                )

            }

            const query =
                params.toString()

            return query
                ? `/export-csv?${query}`
                : '/export-csv'

        }

    },


    methods: {

        /* =========================================================
           INITIAL
        ========================================================== */

        getInitial(title) {

            if (!title) {

                return 'T'

            }

            return title
                .charAt(0)
                .toUpperCase()

        },


        /* =========================================================
           SUCCESS MESSAGE
        ========================================================== */

        showMessage(message) {

            this.successMessage =
                message

            this.showSuccess =
                true

            setTimeout(() => {

                this.showSuccess =
                    false

            }, 4000)

        },


        /* =========================================================
           PAGE URL
        ========================================================== */

        pageUrl(page) {

            const params =
                new URLSearchParams(
                    window.location.search
                )

            params.set(
                'page',
                page
            )

            return `/?${params.toString()}`

        },


        /* =========================================================
           SORT URL
        ========================================================== */

        sortUrl(field) {

            const params =
                new URLSearchParams(
                    window.location.search
                )

            const currentSort =
                params.get('sort')

            const currentDirection =
                params.get('direction')

            let direction =
                'asc'

            if (
                currentSort === field
            ) {

                direction =
                    currentDirection ===
                    'asc'
                        ? 'desc'
                        : 'asc'

            }

            params.set(
                'sort',
                field
            )

            params.set(
                'direction',
                direction
            )

            return `/?${params.toString()}`

        },


        /* =========================================================
           SORT ICON
        ========================================================== */

        sortIcon(field) {

            const params =
                new URLSearchParams(
                    window.location.search
                )

            if (
                params.get('sort') !== field
            ) {

                return '↕'

            }

            return params.get(
                'direction'
            ) === 'asc'
                ? '↑'
                : '↓'

        },


        /* =========================================================
           SELECT ALL
        ========================================================== */

        toggleSelectAll() {

            if (this.allSelected) {

                this.selectedIds = []

            } else {

                this.selectedIds =
                    this.titles.data.map(
                        item => item.id
                    )

            }

        },


        /* =========================================================
           BULK DELETE
        ========================================================== */

        bulkDelete() {

            if (
                this.selectedIds.length === 0
            ) {

                return

            }

            if (
                !confirm(
                    `Delete ${this.selectedIds.length} selected title(s)?`
                )
            ) {

                return

            }

            const formData =
                new FormData()

            formData.append(
                '_token',
                this.csrf
            )

            this.selectedIds.forEach(
                id => {

                    formData.append(
                        'ids[]',
                        id
                    )

                }
            )

            fetch(
                '/bulk-delete',
                {

                    method: 'POST',

                    body: formData,

                    headers: {

                        'X-Requested-With':
                            'XMLHttpRequest',

                        'Accept':
                            'application/json'

                    }

                }
            )
            .then(response => {

                if (!response.ok) {

                    throw new Error(
                        'Bulk delete failed'
                    )

                }

                const contentType =
                    response.headers.get(
                        'content-type'
                    )

                if (
                    contentType &&
                    contentType.includes(
                        'application/json'
                    )
                ) {

                    return response.json()

                }

                return {

                    success: true,

                    message:
                        'Selected titles deleted successfully!'

                }

            })
            .then(data => {

                this.showMessage(
                    data.message ||
                    'Selected titles deleted successfully!'
                )

                setTimeout(() => {

                    window.location.reload()

                }, 900)

            })
            .catch(error => {

                console.error(error)

                alert(
                    'Unable to delete selected titles.'
                )

            })

        },


        /* =========================================================
           FAVORITE
        ========================================================== */

        toggleFavorite(item) {

            fetch(
                `/favorite/${item.id}`,
                {

                    method: 'POST',

                    headers: {

                        'X-CSRF-TOKEN':
                            this.csrf,

                        'X-Requested-With':
                            'XMLHttpRequest',

                        'Accept':
                            'application/json'

                    }

                }
            )
            .then(response => {

                if (!response.ok) {

                    throw new Error(
                        'Favorite update failed'
                    )

                }

                return response.json()

            })
            .then(data => {

                item.is_favorite =
                    data.is_favorite

                this.showMessage(
                    data.message ||
                    'Favorite updated successfully!'
                )

            })
            .catch(error => {

                console.error(error)

                alert(
                    'Unable to update favorite.'
                )

            })

        },


        /* =========================================================
           QUICK STATUS
        ========================================================== */

        changeStatus(
            item,
            newStatus
        ) {

            const oldStatus =
                item.status

            item.status =
                newStatus

            const formData =
                new FormData()

            formData.append(
                '_token',
                this.csrf
            )

            formData.append(
                'status',
                newStatus
            )

            fetch(
                `/status/${item.id}`,
                {

                    method: 'POST',

                    body: formData,

                    headers: {

                        'X-Requested-With':
                            'XMLHttpRequest',

                        'Accept':
                            'application/json'

                    }

                }
            )
            .then(response => {

                if (!response.ok) {

                    throw new Error(
                        'Status update failed'
                    )

                }

                return response.json()

            })
            .then(data => {

                item.status =
                    data.status

                this.showMessage(
                    data.message ||
                    'Status updated successfully!'
                )

            })
            .catch(error => {

                console.error(error)

                item.status =
                    oldStatus

                alert(
                    'Unable to update status.'
                )

            })

        }

    }

}

</script>


<style scoped>

/* ================================================================
   GLOBAL
================================================================ */

.title-management {

    min-height: 100vh;

    padding: 28px;

    background:
        #f6f8fb;

    color:
        #1e293b;

    font-family:
        Inter,
        -apple-system,
        BlinkMacSystemFont,
        "Segoe UI",
        sans-serif;

}


/* ================================================================
   PAGE HEADER
================================================================ */

.page-header {

    display: flex;

    justify-content: space-between;

    align-items: center;

    gap: 20px;

    margin-bottom: 25px;

}


.header-left {

    display: flex;

    align-items: center;

    gap: 15px;

}


.page-icon {

    width: 52px;

    height: 52px;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 14px;

    background:
        #e9efff;

    font-size: 25px;

}


.page-header h1 {

    margin: 0;

    font-size: 27px;

    font-weight: 750;

    color:
        #111827;

}


.page-header p {

    margin: 4px 0 0;

    color:
        #64748b;

    font-size: 14px;

}


.header-actions {

    display: flex;

    gap: 10px;

}


/* ================================================================
   BUTTONS
================================================================ */

.btn-modern {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 8px;

    min-height: 42px;

    padding:
        0 17px;

    border-radius: 9px;

    text-decoration: none;

    font-size: 14px;

    font-weight: 650;

    transition:
        all .2s ease;

}


.btn-modern:hover {

    transform:
        translateY(-1px);

}


.btn-export {

    background:
        #ffffff;

    border:
        1px solid #dce3ec;

    color:
        #334155;

}


.btn-export:hover {

    background:
        #f8fafc;

    color:
        #0f172a;

}


.btn-create {

    background:
        #4f46e5;

    color:
        white;

    border:
        1px solid #4f46e5;

}


.btn-create:hover {

    background:
        #4338ca;

    color:
        white;

}


/* ================================================================
   STATS
================================================================ */

.stats-grid {

    display: grid;

    grid-template-columns:
        repeat(4, 1fr);

    gap: 16px;

    margin-bottom: 22px;

}


.stat-card {

    background:
        #ffffff;

    border:
        1px solid #e7ebf1;

    border-radius:
        13px;

    padding:
        19px;

    display: flex;

    align-items: center;

    gap: 14px;

    box-shadow:
        0 2px 8px rgba(
            15,
            23,
            42,
            .03
        );

}


.stat-icon {

    width: 45px;

    height: 45px;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 11px;

    font-size: 20px;

}


.total-icon {

    background:
        #eef2ff;

}


.published-icon {

    background:
        #ecfdf5;

}


.draft-icon {

    background:
        #fff7ed;

}


.favorite-icon {

    background:
        #fff7ed;

}


.stat-info {

    display: flex;

    flex-direction: column;

}


.stat-label {

    font-size: 12px;

    color:
        #64748b;

    margin-bottom: 3px;

}


.stat-info strong {

    font-size: 22px;

    color:
        #111827;

}


/* ================================================================
   MAIN CARD
================================================================ */

.main-card {

    background:
        #ffffff;

    border:
        1px solid #e6eaf0;

    border-radius:
        14px;

    box-shadow:
        0 4px 16px rgba(
            15,
            23,
            42,
            .04
        );

    overflow:
        hidden;

}


/* ================================================================
   FILTER HEADER
================================================================ */

.filter-header {

    display: flex;

    justify-content:
        space-between;

    align-items:
        center;

    padding:
        22px 24px;

    border-bottom:
        1px solid #edf0f4;

}


.filter-header h2 {

    margin:
        0 0 4px;

    font-size:
        17px;

    font-weight:
        700;

    color:
        #111827;

}


.filter-header p {

    margin:
        0;

    font-size:
        13px;

    color:
        #64748b;

}


.reset-filter {

    color:
        #64748b;

    font-size:
        13px;

    font-weight:
        600;

    text-decoration:
        none;

}


.reset-filter:hover {

    color:
        #4f46e5;

}


/* ================================================================
   FILTER FORM
================================================================ */

.filter-form {

    padding:
        20px 24px;

    display:
        grid;

    grid-template-columns:
        2fr 1fr 1fr 1fr 1fr auto;

    gap:
        14px;

    border-bottom:
        1px solid #edf0f4;

}


.filter-group {

    min-width:
        0;

}


.filter-group label {

    display:
        block;

    margin-bottom:
        7px;

    font-size:
        12px;

    font-weight:
        700;

    color:
        #475569;

}


.filter-group input,
.filter-group select {

    width:
        100%;

    height:
        40px;

    padding:
        0 11px;

    border:
        1px solid #dbe1e8;

    border-radius:
        8px;

    background:
        #ffffff;

    color:
        #1e293b;

    font-size:
        13px;

    outline:
        none;

    transition:
        border .2s,
        box-shadow .2s;

}


.filter-group input:focus,
.filter-group select:focus {

    border-color:
        #818cf8;

    box-shadow:
        0 0 0 3px
        rgba(
            99,
            102,
            241,
            .10
        );

}


.input-wrapper {

    position:
        relative;

}


.input-wrapper input {

    padding-left:
        35px;

}


.input-icon {

    position:
        absolute;

    left:
        11px;

    top:
        50%;

    transform:
        translateY(-50%);

    font-size:
        14px;

    opacity:
        .6;

}


.filter-button-wrapper {

    display:
        flex;

    align-items:
        end;

}


.apply-filter {

    height:
        40px;

    padding:
        0 16px;

    border:
        none;

    border-radius:
        8px;

    background:
        #4f46e5;

    color:
        #ffffff;

    font-size:
        13px;

    font-weight:
        650;

    cursor:
        pointer;

}


.apply-filter:hover {

    background:
        #4338ca;

}


/* ================================================================
   TOOLBAR
================================================================ */

.table-toolbar {

    display:
        flex;

    justify-content:
        space-between;

    align-items:
        center;

    padding:
        14px 24px;

    background:
        #fafbfc;

    border-bottom:
        1px solid #edf0f4;

}


.selection-info {

    display:
        flex;

    align-items:
        center;

    gap:
        9px;

    color:
        #475569;

    font-size:
        13px;

    font-weight:
        600;

}


.selected-count {

    padding:
        3px 8px;

    border-radius:
        20px;

    background:
        #eef2ff;

    color:
        #4f46e5;

    font-size:
        11px;

}


.bulk-delete-btn {

    height:
        34px;

    padding:
        0 12px;

    border:
        1px solid #fecaca;

    border-radius:
        7px;

    background:
        #fff1f2;

    color:
        #dc2626;

    font-size:
        12px;

    font-weight:
        650;

    cursor:
        pointer;

}


.bulk-delete-btn:disabled {

    opacity:
        .45;

    cursor:
        not-allowed;

}


/* ================================================================
   CHECKBOX
================================================================ */

.custom-checkbox {

    display:
        inline-flex;

    align-items:
        center;

    cursor:
        pointer;

}


.custom-checkbox input {

    display:
        none;

}


.checkmark {

    width:
        17px;

    height:
        17px;

    display:
        block;

    border:
        1.5px solid #cbd5e1;

    border-radius:
        5px;

    background:
        #ffffff;

    position:
        relative;

}


.custom-checkbox input:checked +
.checkmark {

    background:
        #4f46e5;

    border-color:
        #4f46e5;

}


.custom-checkbox input:checked +
.checkmark::after {

    content:
        "✓";

    position:
        absolute;

    color:
        white;

    font-size:
        11px;

    font-weight:
        700;

    left:
        3px;

    top:
        0px;

}


/* ================================================================
   TABLE
================================================================ */

.table-container {

    overflow-x:
        auto;

}


.modern-table {

    width:
        100%;

    border-collapse:
        collapse;

    min-width:
        1100px;

}


.modern-table thead {

    background:
        #f8fafc;

}


.modern-table th {

    padding:
        13px 15px;

    text-align:
        left;

    border-bottom:
        1px solid #e5e7eb;

    color:
        #64748b;

    font-size:
        11px;

    text-transform:
        uppercase;

    letter-spacing:
        .04em;

    font-weight:
        750;

}


.modern-table td {

    padding:
        14px 15px;

    border-bottom:
        1px solid #f0f2f5;

    vertical-align:
        middle;

}


.table-row {

    transition:
        background .15s ease;

}


.table-row:hover {

    background:
        #fafbff;

}


.checkbox-column {

    width:
        45px;

}


.text-center {

    text-align:
        center !important;

}


/* ================================================================
   SORT
================================================================ */

.sort-link {

    display:
        inline-flex;

    align-items:
        center;

    gap:
        5px;

    color:
        #475569;

    text-decoration:
        none;

}


.sort-link:hover {

    color:
        #4f46e5;

}


.sort-link span {

    color:
        #94a3b8;

}


/* ================================================================
   ID
================================================================ */

.id-badge {

    display:
        inline-flex;

    padding:
        4px 8px;

    border-radius:
        6px;

    background:
        #f1f5f9;

    color:
        #64748b;

    font-size:
        11px;

    font-weight:
        700;

}


/* ================================================================
   TITLE CELL
================================================================ */

.title-cell {

    display:
        flex;

    align-items:
        center;

    gap:
        11px;

}


.title-avatar {

    width:
        38px;

    height:
        38px;

    flex-shrink:
        0;

    display:
        flex;

    align-items:
        center;

    justify-content:
        center;

    border-radius:
        10px;

    background:
        #eef2ff;

    color:
        #4f46e5;

    font-weight:
        750;

    font-size:
        14px;

}


.title-name {

    font-size:
        13px;

    font-weight:
        700;

    color:
        #1e293b;

    max-width:
        220px;

    overflow:
        hidden;

    text-overflow:
        ellipsis;

    white-space:
        nowrap;

}


.title-meta {

    margin-top:
        2px;

    font-size:
        10px;

    color:
        #94a3b8;

}


/* ================================================================
   DESCRIPTION
================================================================ */

.description-cell {

    max-width:
        320px;

    color:
        #64748b;

    font-size:
        12px;

    line-height:
        1.5;

    display:
        -webkit-box;

    -webkit-line-clamp:
        2;

    -webkit-box-orient:
        vertical;

    overflow:
        hidden;

}


/* ================================================================
   STATUS
================================================================ */

.status-wrapper {

    display:
        inline-flex;

    align-items:
        center;

    gap:
        6px;

}


.status-dot {

    width:
        7px;

    height:
        7px;

    border-radius:
        50%;

    display:
        block;

}


.status-dot.status-published {

    background:
        #10b981;

}


.status-dot.status-draft {

    background:
        #f59e0b;

}


.status-dot.status-archived {

    background:
        #94a3b8;

}


.status-select {

    border:
        none;

    outline:
        none;

    background:
        transparent;

    font-size:
        12px;

    font-weight:
        650;

    cursor:
        pointer;

}


.status-select.status-published {

    color:
        #059669;

}


.status-select.status-draft {

    color:
        #d97706;

}


.status-select.status-archived {

    color:
        #64748b;

}


/* ================================================================
   FAVORITE
================================================================ */

.favorite-button {

    width:
        36px;

    height:
        36px;

    border:
        1px solid #e2e8f0;

    border-radius:
        9px;

    background:
        #ffffff;

    color:
        #94a3b8;

    font-size:
        20px;

    cursor:
        pointer;

    transition:
        all .2s ease;

}


.favorite-button:hover {

    transform:
        scale(1.06);

    border-color:
        #fbbf24;

}


.favorite-button.active {

    background:
        #fffbeb;

    border-color:
        #fcd34d;

    color:
        #f59e0b;

}


/* ================================================================
   ACTIONS
================================================================ */

.action-buttons {

    display:
        flex;

    flex-wrap:
        wrap;

    gap:
        5px;

}


.action-btn {

    display:
        inline-flex;

    align-items:
        center;

    gap:
        4px;

    height:
        30px;

    padding:
        0 8px;

    border:
        1px solid transparent;

    border-radius:
        6px;

    text-decoration:
        none;

    font-size:
        11px;

    font-weight:
        650;

    transition:
        all .15s ease;

}


.edit-btn {

    color:
        #d97706;

    background:
        #fffbeb;

    border-color:
        #fde68a;

}


.edit-btn:hover {

    background:
        #fef3c7;

}


.preview-btn {

    color:
        #0284c7;

    background:
        #f0f9ff;

    border-color:
        #bae6fd;

}


.preview-btn:hover {

    background:
        #e0f2fe;

}


.duplicate-btn {

    color:
        #64748b;

    background:
        #f8fafc;

    border-color:
        #e2e8f0;

}


.duplicate-btn:hover {

    background:
        #f1f5f9;

}


.delete-btn {

    color:
        #dc2626;

    background:
        #fff1f2;

    border-color:
        #fecdd3;

}


.delete-btn:hover {

    background:
        #ffe4e6;

}


/* ================================================================
   EMPTY STATE
================================================================ */

.empty-state {

    padding:
        70px 20px !important;

    text-align:
        center;

}


.empty-icon {

    width:
        65px;

    height:
        65px;

    display:
        flex;

    align-items:
        center;

    justify-content:
        center;

    margin:
        0 auto 14px;

    border-radius:
        50%;

    background:
        #f1f5f9;

    font-size:
        27px;

}


.empty-state h3 {

    margin:
        0 0 5px;

    color:
        #334155;

    font-size:
        16px;

}


.empty-state p {

    margin:
        0 0 17px;

    color:
        #94a3b8;

    font-size:
        13px;

}


.empty-create {

    display:
        inline-flex;

}


/* ================================================================
   PAGINATION
================================================================ */

.pagination-container {

    display:
        flex;

    justify-content:
        space-between;

    align-items:
        center;

    padding:
        18px 24px;

}


.pagination-info {

    color:
        #64748b;

    font-size:
        12px;

}


.pagination-info strong {

    color:
        #334155;

}


.modern-pagination {

    list-style:
        none;

    display:
        flex;

    gap:
        5px;

    margin:
        0;

    padding:
        0;

}


.modern-pagination li a {

    width:
        32px;

    height:
        32px;

    display:
        flex;

    align-items:
        center;

    justify-content:
        center;

    border:
        1px solid #e2e8f0;

    border-radius:
        7px;

    text-decoration:
        none;

    color:
        #475569;

    background:
        #ffffff;

    font-size:
        12px;

    font-weight:
        650;

}


.modern-pagination li a:hover {

    border-color:
        #818cf8;

    color:
        #4f46e5;

}


.modern-pagination li.active a {

    background:
        #4f46e5;

    color:
        #ffffff;

    border-color:
        #4f46e5;

}


.modern-pagination li.disabled a {

    opacity:
        .35;

    pointer-events:
        none;

}


/* ================================================================
   SUCCESS NOTIFICATION
================================================================ */

.success-notification {

    position:
        fixed;

    top:
        22px;

    right:
        25px;

    z-index:
        9999;

    min-width:
        320px;

    max-width:
        450px;

    display:
        flex;

    align-items:
        center;

    gap:
        12px;

    padding:
        14px 16px;

    background:
        #ffffff;

    border:
        1px solid #d1fae5;

    border-left:
        4px solid #10b981;

    border-radius:
        10px;

    box-shadow:
        0 10px 30px
        rgba(
            15,
            23,
            42,
            .12
        );

}


.success-icon {

    width:
        34px;

    height:
        34px;

    display:
        flex;

    align-items:
        center;

    justify-content:
        center;

    border-radius:
        50%;

    background:
        #ecfdf5;

    color:
        #059669;

    font-weight:
        800;

}


.success-content {

    display:
        flex;

    flex-direction:
        column;

    gap:
        2px;

    flex:
        1;

}


.success-content strong {

    color:
        #065f46;

    font-size:
        13px;

}


.success-content span {

    color:
        #64748b;

    font-size:
        12px;

}


.notification-close {

    border:
        none;

    background:
        transparent;

    color:
        #94a3b8;

    font-size:
        21px;

    cursor:
        pointer;

}


/* ================================================================
   TRANSITION
================================================================ */

.slide-down-enter-active,
.slide-down-leave-active {

    transition:
        all .3s ease;

}


.slide-down-enter-from,
.slide-down-leave-to {

    opacity:
        0;

    transform:
        translateY(-15px);

}


/* ================================================================
   RESPONSIVE
================================================================ */

@media (
    max-width: 1200px
) {

    .filter-form {

        grid-template-columns:
            repeat(3, 1fr);

    }

    .search-group {

        grid-column:
            span 2;

    }

    .stats-grid {

        grid-template-columns:
            repeat(2, 1fr);

    }

}


@media (
    max-width: 768px
) {

    .title-management {

        padding:
            15px;

    }


    .page-header {

        align-items:
            flex-start;

        flex-direction:
            column;

    }


    .header-actions {

        width:
            100%;

    }


    .btn-modern {

        flex:
            1;

    }


    .filter-form {

        grid-template-columns:
            1fr;

    }


    .search-group {

        grid-column:
            auto;

    }


    .stats-grid {

        grid-template-columns:
            1fr;

    }


    .filter-header {

        align-items:
            flex-start;

        gap:
            10px;

        flex-direction:
            column;

    }


    .table-toolbar {

        align-items:
            flex-start;

        gap:
            10px;

        flex-direction:
            column;

    }


    .pagination-container {

        align-items:
            flex-start;

        gap:
            15px;

        flex-direction:
            column;

    }


    .success-notification {

        left:
            15px;

        right:
            15px;

        min-width:
            auto;

    }

}

</style>