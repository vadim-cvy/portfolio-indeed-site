<div class="pjs-filter__secondary-controls">
  <div
    class="pjs-filter__secondary-controls__control"
    v-for="(control, controlIndex) in controls.secondary"
    :key="controlIndex"
  >
    <a href="#" class="pjs-filter__secondary-controls__control__label">
      {{ control.label }}
    </a>

    <div class="pjs-filter__secondary-controls__control__options">
      <a
        href="#"
        v-for="(option, optionIndex) in control.options"
        :key="optionIndex"
        @click="() => control.val = option.val"
        class="pjs-filter__secondary-controls__control__options__option"
      >
        {{ option.label }}
      </a>
    </div>
  </div>
</div>