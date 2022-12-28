import { TextInput } from "react-native-paper";
import React from "react";
import { COLORS, SIZES } from "../../../../constants";

export const ModalInput = ({label}) => {
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
    fontSize: SIZES.fs16,
  }
}
