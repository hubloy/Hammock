import React, { PureComponent } from 'react';

export class CheckBox extends PureComponent {

	constructor(props) {
		super(props);
		this.state = { checked: undefined };
	}

	onChange(e) {
		var checked = !!e.target.checked;
		this.setState({ checked: checked });
		if(typeof this.props.action !== 'undefined' && typeof this.props.action === 'function') {
			var action = this.props.action;
			action( checked );
		}
	}


	render() {
		const checked = typeof this.state.checked === 'undefined' ? this.props.checked : this.state.checked;
		var disabled = window.hubloy_membership.common.status.disabled;
		return (
			<div className="switch-checkbox">
				<div className="switch b2">
					<input type="checkbox"
						className={'checkbox'}
						name={this.props.name}
						value={this.props.value}
						checked={checked}
						onChange={this.onChange.bind(this)} 
					/>
					<div className="knobs">
						<span>{disabled}</span>
					</div>
				</div>
			</div>
		)
	}
}