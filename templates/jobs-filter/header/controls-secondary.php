<div class="pjs-filter__controls_secondary">
  <div
    class="pjs-filter__control pjs-filter__control_type_select"
    v-for="(control, controlIndex) in controls.secondary"
    :key="controlIndex"
  >
    <span class="pjs-filter__control__label">
      {{ control.label }}
    </span>

    <div class="pjs-filter__control__options">
      <a
        href="#"
        v-for="(option, optionIndex) in control.options"
        :key="optionIndex"
        @click="() => control.val = option.val"
      >
        {{ option.label }}
      </a>
    </div>
  </div>
</div>