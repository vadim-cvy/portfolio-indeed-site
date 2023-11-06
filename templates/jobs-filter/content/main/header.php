<div class="pjs-filter__content__column-main__header">
  <div class="pjs-filter__search-term">
    {{ searchTermVal }} jobs
  </div>

  <div class="pjs-filter__sorting">
    {{ sortingControl.label }}:

    <a
      href="#"
      v-for="option in sortingControl.options"
      :key="option.val"
      @click="() => sortingControl.toggleSelection( option )"
      :class="{
        'pjs-filter__sorting__option': true,
        'pjs-filter__sorting__option_is-selected': sortingControl.selectedOption === option,
      }"
    >
      {{ option.label }}
    </a>
  </div>

  <div class="pjs-filter__total-matches">
    {{ jobs.length }} jobs
  </div>
</div>