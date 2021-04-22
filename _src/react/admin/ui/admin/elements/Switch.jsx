import React, { PureComponent } from 'react';
import PropTypes from 'prop-types';

export class SwitchUI extends PureComponent {
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
		const title = checked ? ( this.props.enabled_title ? this.props.enabled_title : this.props.title ) : this.props.title;
		var data = Array(),
			target = this.props.attributes;
		for (var k in target) {
			if (target.hasOwnProperty(k)) {
				data.push(k + '=' + target[k]);
			}
		}
		data = data.join(' ');
		return (
			<section className="slider-checkbox">
				<input
					type="checkbox"
					className={this.props.class_name}
					{...data}
					name={this.props.name}
					value={this.props.value}
					checked={checked}
					onChange={this.onChange.bind(this)}
				/>
				<label className={'label ' + this.props.label_class} htmlFor={this.props.name}>
					{title}
				</label>
			</section>
		);
	}
}
