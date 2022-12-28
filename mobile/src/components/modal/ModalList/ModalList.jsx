import { TextInput } from "react-native-paper";
import React from "react";
import { COLORS, SIZES } from "../../../../constants";

export const ModalList = ({label}) => {
  const [text, setText] = React.useState('');
  return (
    <TextInput
      style={styles.input}
      label={label}
      value={text}
      onChangeText={text => setText(text)}
    />
  )
}
const styles = {
  input: {
    borderRadius: 4,
    backgroundColor: COLORS.white,
    borderWidth: 1,
    borderColor: '#0000001f',
    fontSize: SIZES.fs16,
  }
}
