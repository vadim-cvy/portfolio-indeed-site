<div class="pjs-filter__secondary-controls">
  <div
    class="pjs-filter__secondary-controls__control"
    v-for="(control, controlIndex) in secondaryControls"
    :key="controlIndex"
  >
    <a href="#" class="pjs-filter__secondary-controls__control__label">
      {{ control.selectedOption ? control.selectedOption.label : control.label }}
    </a>

    <div class="pjs-filter__secondary-controls__control__options">
      <a
        href="#"
        v-for="option in control.options"
        :key="option.val"
        @click="() => control.toggleSelection( option )"
        class="pjs-filter__secondary-controls__control__options__option"
      >
        {{ option.label }}
      </a>
    </div>
  </div>
</div>