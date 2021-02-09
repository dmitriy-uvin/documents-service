<template>
    <div><b-dropdown
        v-model="navigationOpen"
        position="is-bottom-left"
        aria-role="menu"
    >
        <template #trigger>
            <a
                class="navbar-item"
                role="button"
            >
                <span>Дмитрий</span>
                <b-icon icon="chevron-down"></b-icon>
            </a>
        </template>
        <b-dropdown-item has-link aria-role="menuitem">
            <a href="https://google.com" target="_blank">
                <b-icon icon="link"></b-icon>
                Google (link)
            </a>
        </b-dropdown-item>
        <hr class="dropdown-divider" aria-role="menuitem">
        <b-dropdown-item value="profile">
            <b-icon icon="user"></b-icon>
            Профиль
        </b-dropdown-item>
        <b-dropdown-item value="logout" aria-role="menuitem">
            <b-icon icon="times"></b-icon>
            Выйти
        </b-dropdown-item>
    </b-dropdown></div>
</template>

<script>
export default {
    name: "UserDropdown",
    data: () => ({
        navigationOpen: false
    })
}
</script>

<style scoped>

</style>
