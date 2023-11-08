<div class="pjs-filter__content__column-main__header">
  <div class="pjs-filter__search-term">
    {{ controls.searchTerm.val }} jobs
  </div>

  <div class="pjs-filter__sorting">
    Sort by:

    <a
      href="#"
      v-for="option in controls.sorting.options"
      :key="option.val"
      @click="() => updateControlVal( controls.sorting, option.val )"
      :class="{
        'pjs-filter__sorting__option': true,
        'pjs-filter__sorting__option_is-selected': controls.sorting.val === option.val,
      }"
    >
      {{ option.label }}
    </a>
  </div>

  <div class="pjs-filter__total-matches">
    {{ matches.length }} jobs
  </div>
</div>