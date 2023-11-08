import Option from "./Option"
import OptionsControl from "./OptionsControl"

export default class DropdownControl extends OptionsControl
{
  public constructor(
    name: string,
    options: Option[],
    defaultOptionLabel: string,
  )
  {
    const defaultOption = new Option( '', defaultOptionLabel )

    options.unshift( defaultOption )

    super( name, options )
  }

  public get selectedOptionLabel()
  {
    return this.options.filter( option => option.val === this._val )[0].label
  }
}