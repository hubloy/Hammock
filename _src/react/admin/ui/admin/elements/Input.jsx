import React, { PureComponent } from 'react';

export class InputUI extends PureComponent {

	constructor(props) {
		super(props);
		this.state = { value: undefined };
		this.onChange = this.onChange.bind(this);
	}

	onChange(e) {
		this.setState({ value: e.target.value });
	}
	
	render() {
		const { name,type='text', placeholder = '', class_name = '', required = false } = this.props;
		const value = typeof this.state.value === 'undefined' ? this.props.value : this.state.value;
		return (
			<input type={type} name={name} className={'uk-input ' +name + ' ' + class_name} value={value} placeholder={placeholder} required={required} onChange={this.onChange}/>
		)
	}
}