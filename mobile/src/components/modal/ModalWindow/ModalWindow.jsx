import React from 'react';
import { Image, View } from 'react-native';
import { magicModal } from 'react-native-magic-modal';
import { Button } from "react-native-paper";
import { TextInput } from 'react-native-paper';
import { COLORS, SIZES } from "../../../../constants";

export const ModalWindow = () => {
  const [text, setText] = React.useState('');

  return (
    <View style={styles.container}>
      <Button
        onPress={() => magicModal.hide('close button pressed')}
        style={styles.btn}
        icon={() => (
          <Image
            source={require('../../../../assets/close.webp')}
            style={{ width: 30, height: 29 }}
          />
        )} />
      <TextInput
        style={styles.input}
        label="Поиск услуги"
        value={text}
        onChangeText={text => setText(text)}
      />
    </View>
  );
};

const styles = {
  container: {
    backgroundColor: 'transparent',
    margin: 16,
    position: 'relative'
  },
  btn: {
    position: 'absolute',
    top: -50,
    right: -20
  },
  input: {
    borderRadius: 4,
    backgroundColor: COLORS.white,
    borderWidth: 1,
    borderColor: '#0000001f',
    fontSize: SIZES.fs16,
  }
}
