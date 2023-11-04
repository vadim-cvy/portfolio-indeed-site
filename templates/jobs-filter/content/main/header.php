<div class="pjs-filter__content__column-main__header">
  <div class="pjs-filter__search-term">
    {{ controls.main.searchTerm.val }} jobs
  </div>

  <div class="pjs-filter__sorting-controls">
    Sort by:

    <a
      href="#"
      v-for="(control, controlIndex) controls.sorting"
      :key="controlIndex"
      @click="() => sortBy( control )"
    >
      {{ control.label }}
    </a>
  </div>

  <div class="pjs-filter__total-matches">
    {{ totalMatches }} jobs
  </div>
</div>