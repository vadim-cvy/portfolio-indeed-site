<div class="pjs-filter__result__column_items__header">
  <div class="pjs-filter__search-term">
    {{ controls.main.searchTerm.val }} jobs
  </div>

  <div class="pjs-filter__controls_sorting">
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