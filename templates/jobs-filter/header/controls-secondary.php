<div class="pjs-filter__secondary-controls">
  <div
    class="pjs-filter__secondary-controls__control"
    v-for="control in controls.secondary"
    :key="control.name"
  >
    <a href="#" class="pjs-filter__secondary-controls__control__label">
      {{ control.selectedOptionLabel }}
    </a>

    <div class="pjs-filter__secondary-controls__control__options">
      <a
        href="#"
        v-for="option in control.options"
        :key="option.val"
        @click="() => updateControlVal( control, option.val )"
        class="pjs-filter__secondary-controls__control__options__option"
      >
        {{ option.label }}
      </a>
    </div>
  </div>
</div>